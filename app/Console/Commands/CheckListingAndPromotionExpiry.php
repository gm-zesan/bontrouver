<?php

namespace App\Console\Commands;

use App\Enums\ListingStatus;
use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Notifications\ListingBoostExpired;
use App\Notifications\ListingBoostExpiringSoon;
use App\Notifications\ListingExpired;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckListingAndPromotionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listings:check-expiry {--dry-run : Run without making database changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and deactivates expired listing promotions (Sponsored/Featured) and expired listings, sending internal notifications.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $now = Carbon::now();
        $this->info("Starting Listing and Promotion Expiry Check at {$now->toDateTimeString()} (Dry Run: " . ($isDryRun ? 'YES' : 'NO') . ")");

        $expiredPromosCount = 0;
        $expiringWarningCount = 0;
        $expiredListingsCount = 0;

        // ── 1. Expire Active Promotions ──────────────────────────────────────────
        $expiredPromotions = ListingPromotion::with(['listing.user', 'package'])
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get();

        foreach ($expiredPromotions as $promo) {
            $listing = $promo->listing;
            $user = $listing?->user ?? User::find($promo->user_id);

            $this->line("• Expiring {$promo->type} promo ID #{$promo->id} for listing #{$listing?->id} ('{$listing?->title}')");

            if (!$isDryRun) {
                DB::transaction(function () use ($promo, $listing, $user) {
                    $promo->update(['is_active' => false]);

                    if ($listing) {
                        // Check if listing has any other active promotions of the same type
                        $hasOtherActive = ListingPromotion::where('listing_id', $listing->id)
                            ->where('type', $promo->type)
                            ->where('is_active', true)
                            ->where('id', '!=', $promo->id)
                            ->exists();

                        if (!$hasOtherActive) {
                            if ($promo->type === 'sponsored') {
                                $listing->update([
                                    'is_sponsored' => false,
                                    'sponsored_until' => null,
                                ]);
                            } elseif ($promo->type === 'featured') {
                                $listing->update([
                                    'is_featured' => false,
                                    'featured_until' => null,
                                ]);
                            }
                        }
                    }

                    if ($user && $listing) {
                        try {
                            $user->notify(new ListingBoostExpired($listing, $promo));
                        } catch (\Throwable $e) {
                            Log::error("Failed to notify user about expired boost: " . $e->getMessage());
                        }
                    }
                });
            }

            $expiredPromosCount++;
        }

        // ── 2. 24-Hour Expiry Warning Notifications ──────────────────────────────
        $warningWindowStart = $now->copy();
        $warningWindowEnd = $now->copy()->addHours(24);

        $expiringPromotions = ListingPromotion::with(['listing.user'])
            ->where('is_active', true)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$warningWindowStart, $warningWindowEnd])
            ->get();

        foreach ($expiringPromotions as $promo) {
            $user = $promo->listing?->user;
            if (!$user || !$promo->listing) {
                continue;
            }

            // Check if warning notification was already sent in the last 24h
            $alreadyNotified = $user->notifications()
                ->where('type', ListingBoostExpiringSoon::class)
                ->where('data->listing_id', $promo->listing_id)
                ->where('data->package_type', $promo->type)
                ->where('created_at', '>=', $now->copy()->subHours(24))
                ->exists();

            if (!$alreadyNotified) {
                $this->line("• Sending 24h expiry warning for {$promo->type} promo ID #{$promo->id} to user #{$user->id}");
                if (!$isDryRun) {
                    try {
                        $user->notify(new ListingBoostExpiringSoon($promo->listing, $promo));
                    } catch (\Throwable $e) {
                        Log::error("Failed to send 24h boost expiry warning: " . $e->getMessage());
                    }
                }
                $expiringWarningCount++;
            }
        }

        // ── 3. Expire Stale Listings ─────────────────────────────────────────────
        $expiredListings = Listing::with('user')
            ->where('status', ListingStatus::ACTIVE->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get();

        foreach ($expiredListings as $listing) {
            $this->line("• Expiring listing #{$listing->id} ('{$listing->title}')");

            if (!$isDryRun) {
                $listing->update(['status' => ListingStatus::EXPIRED->value]);
                if ($listing->user) {
                    try {
                        $listing->user->notify(new ListingExpired($listing));
                    } catch (\Throwable $e) {
                        Log::error("Failed to notify user about expired listing: " . $e->getMessage());
                    }
                }
            }

            $expiredListingsCount++;
        }

        $this->info("✅ Expiry check completed:");
        $this->info("   - Expired Promotions Deactivated: {$expiredPromosCount}");
        $this->info("   - 24h Expiry Warnings Dispatched: {$expiringWarningCount}");
        $this->info("   - Expired Listings Updated: {$expiredListingsCount}");

        return Command::SUCCESS;
    }
}
