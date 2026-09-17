<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\User;
use App\Models\UserVerification;
use App\Notifications\VerificationStatusUpdated;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VerificationService
{
    /**
     * Submit an ID verification document for moderation review.
     */
    public function submitDocument(
        User $user, 
        ?string $documentType = null, 
        ?UploadedFile $file = null, 
        ?string $idNumber = null
    ): UserVerification {
        return DB::transaction(function () use ($user, $documentType, $file, $idNumber) {
            $path = $file ? ('/storage/' . $file->store('verifications', 'public')) : ($user->latestVerification?->document_path ?? null);

            // Create new verification record
            $verification = UserVerification::create([
                'user_id' => $user->id,
                'document_type' => $documentType ?: ($user->latestVerification?->document_type ?? 'government_id'),
                'document_path' => $path,
                'id_number' => $idNumber ? (substr($idNumber, 0, 4) . str_repeat('*', max(0, strlen($idNumber) - 4))) : ($user->latestVerification?->id_number ?? null),
                'phone_number' => $user->phone,
                'status' => 'pending',
            ]);

            return $verification;
        });
    }

    /**
     * Get paginated pending verification requests for admin review.
     */
    public function getPendingVerifications(int $perPage = 15): LengthAwarePaginator
    {
        return UserVerification::with(['user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all verification requests with optional status filtering for admin panel.
     */
    public function getAllVerifications(?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = UserVerification::with(['user', 'reviewer'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Review and approve or reject a verification request (Moderator / Admin).
     */
    public function reviewVerification(
        UserVerification $verification, 
        string $status, 
        ?string $reason = null, 
        ?User $reviewer = null
    ): UserVerification {
        return DB::transaction(function () use ($verification, $status, $reason, $reviewer) {
            $verification->status = $status;
            $verification->rejection_reason = $status === 'rejected' ? $reason : null;
            $verification->reviewed_at = now();
            $verification->reviewed_by = $reviewer?->id;
            $verification->save();

            $targetUser = $verification->user;

            if ($status === 'approved') {
                $targetUser->is_verified = true;
                
                // If the document is a registered dealer certificate, also mark as dealer
                if ($verification->document_type === 'dealer_license') {
                    $targetUser->is_dealer = true;
                }

                $targetUser->save();

                // Award 50 community points for identity verification if not previously awarded
                $alreadyAwarded = PointTransaction::where('user_id', $targetUser->id)
                    ->where('action_type', 'verified_identity')
                    ->exists();

                if (!$alreadyAwarded) {
                    PointTransaction::create([
                        'user_id' => $targetUser->id,
                        'points' => 50,
                        'action_type' => 'verified_identity',
                        'reference_type' => UserVerification::class,
                        'reference_id' => $verification->id,
                        'description' => 'Bonus points for completing Canadian ID verification',
                    ]);

                    $targetUser->increment('community_points', 50);
                }
            } elseif ($status === 'rejected') {
                // If user has no other approved verification, mark unverified
                $hasOtherApproved = UserVerification::where('user_id', $targetUser->id)
                    ->where('id', '!=', $verification->id)
                    ->where('status', 'approved')
                    ->exists();

                if (!$hasOtherApproved) {
                    $targetUser->is_verified = false;
                    $targetUser->save();
                }
            }

            // Dispatch notification to user
            $targetUser->notify(new VerificationStatusUpdated($verification, $status, $reason));

            return $verification;
        });
    }
}
