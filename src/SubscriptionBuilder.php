<?php

declare(strict_types=1);

namespace StephenAsare\Paystack;

use Illuminate\Database\Eloquent\Model;
use StephenAsare\Paystack\Models\PaystackSubscription;
use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Contracts\Billable;

class SubscriptionBuilder
{
    /**
     * The billable model.
     *
     * @var Model&Billable
     */
    protected Model $owner;
    protected string $name;
    protected string $plan;
    protected int $quantity = 1;
    protected ?int $trialDays = null;
    protected array $metadata = [];

    public function __construct(Model $owner, string $name, string $plan)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->plan = $plan;
    }

    /**
     * Specify the quantity of the subscription.
     */
    public function quantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Specify the number of days of the trial.
     */
    public function trialDays(int $trialDays): self
    {
        $this->trialDays = $trialDays;
        return $this;
    }

    /**
     * Add metadata to the subscription.
     */
    public function withMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Create the subscription using an existing authorization (card).
     * 
     * @param string $authorization The authorization code (e.g. AUTH_xxx)
     * @param array $customerOptions Options to pass to customer creation if needed
     * @return PaystackSubscription
     * @throws PaystackException|ConnectionException
     */
    public function create(string $authorization, array $customerOptions = []): PaystackSubscription
    {
        // 1. Ensure Customer Exists
        if (! $this->owner->paystackCustomer) {
            $this->owner->createAsPaystackCustomer($customerOptions);
        }

        $customerCode = $this->owner->paystackCustomer->paystack_id;

        // 2. Create Subscription via API
        $payload = [
            'customer' => $customerCode,
            'plan' => $this->plan,
            'authorization' => $authorization,
            'quantity' => $this->quantity,
            'metadata' => $this->metadata,
        ];

        if ($this->trialDays) {
            $payload['start_date'] = now()->addDays($this->trialDays)->toIso8601String();
        }

        $response = app('paystack')->subscription()->create($payload);

        if (! ($response['status'] ?? false)) {
            throw new PaystackException(
                message: "Paystack Subscription Creation Failed: " . ($response['message'] ?? 'Unknown error'),
                httpCode: 400
            );
        }

        $data = $response['data'];

        // 3. Record in Database
        return $this->owner->subscriptions()->create([
            'name' => $this->name,
            'paystack_id' => $data['subscription_code'],
            'paystack_plan' => $this->plan,
            'paystack_status' => $data['status'],
            'quantity' => $this->quantity,
            'email_token' => $data['email_token'] ?? null,
            'trial_ends_at' => $this->trialDays ? now()->addDays($this->trialDays) : null,
        ]);
    }

    /**
     * Initialize a transaction to start the subscription.
     * Use this when you don't have an authorization code yet.
     * 
     * @return array The initialization response containing the authorization URL
     * @throws PaystackException|ConnectionException
     */
    public function checkout(array $customerOptions = []): array
    {
        // 1. Ensure Customer Exists
        if (! $this->owner->paystackCustomer) {
            $this->owner->createAsPaystackCustomer($customerOptions);
        }

        $email = $this->owner->paystackCustomer->email;

        // 2. Initialize Transaction with Plan
        // Note: We don't create the local subscription record yet. 
        // We wait for the webhook to confirm the subscription.
        return app('paystack')->transaction()->initialize([
            'email' => $email,
            'plan' => $this->plan,
            'quantity' => $this->quantity,
            'metadata' => array_merge($this->metadata, [
                'subscription_name' => $this->name, // Pass this so we know which subscription name to use in the webhook
            ]),
        ]);
    }
}
