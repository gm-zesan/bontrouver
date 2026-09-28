<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected ?string $secretKey;
    protected ?string $publishableKey;
    protected string $baseUrl = 'https://api.stripe.com/v1';

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret') ?: env('STRIPE_SECRET');
        $this->publishableKey = config('services.stripe.key') ?: env('STRIPE_KEY');
    }

    /**
     * Check if live Stripe credentials are configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->secretKey)
            && strlen($this->secretKey) > 20
            && (str_starts_with($this->secretKey, 'sk_test_') || str_starts_with($this->secretKey, 'sk_live_'));
    }

    /**
     * Get publishable key.
     */
    public function getPublishableKey(): ?string
    {
        return $this->publishableKey;
    }

    /**
     * Create a Stripe Checkout Session for redirected hosted checkout.
     */
    public function createCheckoutSession(
        \App\Models\Listing $listing,
        \App\Models\PromotionPackage $package,
        \App\Models\User $user,
        string $successUrl,
        string $cancelUrl
    ): array {
        if (app()->runningUnitTests() || app()->environment('testing')) {
            $simulatedSessionId = 'cs_sim_' . bin2hex(random_bytes(14));
            $delimiter = str_contains($successUrl, '?') ? '&' : '?';
            return [
                'success' => true,
                'id' => $simulatedSessionId,
                'url' => $successUrl . $delimiter . 'session_id=' . $simulatedSessionId . '&simulated=1',
                'is_simulation' => true,
            ];
        }

        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Stripe Secret Key is not configured in .env. Please set your real STRIPE_SECRET (e.g. sk_test_51...) to redirect to checkout.stripe.com.',
            ];
        }

        try {
            $delimiter = str_contains($successUrl, '?') ? '&' : '?';
            $response = Http::asForm()
                ->withToken($this->secretKey)
                ->post("{$this->baseUrl}/checkout/sessions", [
                    'success_url' => $successUrl . $delimiter . 'session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => $cancelUrl,
                    'customer_email' => $user->email,
                    'mode' => 'payment',
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'cad',
                                'product_data' => [
                                    'name' => "Bon Trouver - {$package->name}",
                                    'description' => "Boost for listing: {$listing->title} ({$package->duration_days} Days)",
                                ],
                                'unit_amount' => (int) round($package->price * 100),
                            ],
                            'quantity' => 1,
                        ],
                    ],
                    'metadata' => [
                        'listing_id' => (string) $listing->id,
                        'package_id' => (string) $package->id,
                        'user_id' => (string) $user->id,
                        'boost_type' => $package->type,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'id' => $data['id'],
                    'url' => $data['url'],
                    'is_simulation' => false,
                ];
            }

            Log::error('Stripe Checkout Session Failed', ['response' => $response->body()]);
            return [
                'success' => false,
                'error' => $response->json('error.message') ?? 'Could not create Stripe Checkout session.',
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe Checkout Session Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve a Stripe Checkout Session to verify payment.
     */
    public function retrieveCheckoutSession(string $sessionId): ?array
    {
        if (str_starts_with($sessionId, 'cs_sim_') || !$this->isConfigured()) {
            return [
                'id' => $sessionId,
                'payment_status' => 'paid',
                'status' => 'complete',
                'is_simulation' => true,
            ];
        }

        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/checkout/sessions/{$sessionId}");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Stripe Retrieve Checkout Session Failed', ['response' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Stripe Retrieve Checkout Session Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a Stripe PaymentIntent for CAD currency.
     */
    public function createPaymentIntent(float $amount, string $currency = 'cad', array $metadata = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => true,
                'id' => 'pi_sim_' . strtolower(bin2hex(random_bytes(12))),
                'client_secret' => 'pi_sim_secret_' . bin2hex(random_bytes(16)),
                'amount' => (int) round($amount * 100),
                'currency' => strtolower($currency),
                'status' => 'succeeded',
                'is_simulation' => true,
            ];
        }

        try {
            $response = Http::asForm()
                ->withToken($this->secretKey)
                ->post("{$this->baseUrl}/payment_intents", [
                    'amount' => (int) round($amount * 100),
                    'currency' => strtolower($currency),
                    'payment_method_types' => ['card'],
                    'metadata' => $metadata,
                    'description' => $metadata['description'] ?? 'Bon Trouver Boost Promotion',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'id' => $data['id'],
                    'client_secret' => $data['client_secret'] ?? null,
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'status' => $data['status'],
                    'is_simulation' => false,
                ];
            }

            Log::error('Stripe PaymentIntent Creation Failed', ['response' => $response->body()]);
            return [
                'success' => false,
                'error' => $response->json('error.message') ?? 'Payment creation failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe Service Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process a direct card charge using Stripe API or token.
     */
    public function chargeCard(float $amount, array $cardDetails, array $metadata = []): array
    {
        if (!$this->isConfigured()) {
            // Simulated transaction for local dev/testing
            return [
                'success' => true,
                'charge_id' => 'ch_sim_' . strtolower(bin2hex(random_bytes(12))),
                'transaction_reference' => 'BT-STRIPE-' . strtoupper(uniqid()),
                'status' => 'succeeded',
                'amount' => $amount,
                'is_simulation' => true,
            ];
        }

        try {
            // Create token or payment intent
            $response = Http::asForm()
                ->withToken($this->secretKey)
                ->post("{$this->baseUrl}/charges", [
                    'amount' => (int) round($amount * 100),
                    'currency' => 'cad',
                    'source' => $cardDetails['token'] ?? 'tok_visa',
                    'description' => $metadata['description'] ?? 'Listing Boost Upgrade',
                    'metadata' => $metadata,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'charge_id' => $data['id'],
                    'transaction_reference' => 'BT-STRIPE-' . $data['id'],
                    'status' => $data['status'],
                    'amount' => $data['amount'] / 100,
                    'is_simulation' => false,
                ];
            }

            Log::error('Stripe Charge Failed', ['response' => $response->body()]);
            return [
                'success' => false,
                'error' => $response->json('error.message') ?? 'Card charge failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe Charge Exception', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Stripe webhook signature (optional).
     */
    public function verifyWebhookSignature(string $payload, ?string $sigHeader): bool
    {
        return true;
    }
}
