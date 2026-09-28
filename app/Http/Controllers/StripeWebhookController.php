<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingPromotion;
use App\Models\PromotionPackage;
use App\Models\User;
use App\Services\MonetizationService;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected StripeService $stripeService,
        protected MonetizationService $monetizationService
    ) {}

    /**
     * Handle incoming Stripe webhook notifications.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        if (!$this->stripeService->verifyWebhookSignature($payload, $sigHeader)) {
            Log::warning('Stripe Webhook Signature Verification Failed');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);
        $eventType = $event['type'] ?? 'unknown';

        Log::info("Stripe Webhook Received: {$eventType}", ['id' => $event['id'] ?? null]);

        switch ($eventType) {
            case 'checkout.session.completed':
                $this->handlePaymentIntentSucceeded($event['data']['object'] ?? []);
                break;

            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event['data']['object'] ?? []);
                break;

            case 'charge.succeeded':
                $this->handleChargeSucceeded($event['data']['object'] ?? []);
                break;

            default:
                Log::info("Unhandled Stripe Webhook Event: {$eventType}");
                break;
        }

        return response()->json(['status' => 'success', 'event' => $eventType]);
    }

    /**
     * Handle successful PaymentIntent.
     */
    protected function handlePaymentIntentSucceeded(array $paymentIntent): void
    {
        $metadata = $paymentIntent['metadata'] ?? [];
        $listingId = $metadata['listing_id'] ?? null;
        $packageId = $metadata['package_id'] ?? null;
        $userId = $metadata['user_id'] ?? null;
        $paymentIntentId = $paymentIntent['id'] ?? null;

        if ($listingId && $packageId && $userId) {
            $listing = Listing::find($listingId);
            $package = PromotionPackage::find($packageId);
            $user = User::find($userId);

            if ($listing && $package && $user) {
                // Check if already promoted with this reference
                $existing = ListingPromotion::where('transaction_reference', 'BT-STRIPE-' . $paymentIntentId)->first();
                if (!$existing) {
                    $this->monetizationService->promoteListing(
                        listing: $listing,
                        package: $package,
                        user: $user,
                        paymentMethod: 'stripe',
                        transactionRef: 'BT-STRIPE-' . $paymentIntentId
                    );
                }
            }
        }
    }

    /**
     * Handle successful Charge.
     */
    protected function handleChargeSucceeded(array $charge): void
    {
        $chargeId = $charge['id'] ?? null;
        if ($chargeId) {
            ListingPromotion::where('transaction_reference', 'like', "%{$chargeId}%")
                ->update(['payment_status' => 'completed']);
        }
    }
}
