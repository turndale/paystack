<?php

namespace StephenAsare\Paystack\Concerns;

use StephenAsare\Paystack\Models\PaystackSubscription;
use StephenAsare\Paystack\SubscriptionBuilder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ManagesSubscriptions
{
    /**
     * Get the subscriptions relationship.
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(PaystackSubscription::class, 'billable');
    }

    /**
     * Check if currently subscribed by name or plan code.
     *
     * @param string $nameOrPlan Subscription name (e.g. 'default') or Plan Code (e.g. 'PLN_...')
     */
    public function subscribed(string $nameOrPlan = 'default'): bool
    {
        $subscription = $this->subscriptions()
            ->where(function ($query) use ($nameOrPlan) {
                $query->where('name', $nameOrPlan)
                    ->orWhere('paystack_plan', $nameOrPlan);
            })
            ->latest()
            ->first();

        return $subscription ? $subscription->active() : false;
    }

    /**
     * Fluent method to register a new subscription.
     */
    public function recordSubscription(string $name, string $plan, string $subId)
    {
        return $this->subscriptions()->create([
            'name' => $name,
            'paystack_plan' => $plan,
            'paystack_id' => $subId,
            'paystack_status' => 'active',
        ]);
    }

    /**
     * Begin creating a new subscription.
     */
    public function newSubscription(string $name, string $plan): SubscriptionBuilder
    {
        return new SubscriptionBuilder($this, $name, $plan);
    }

    /**
     * Get a subscription instance by name.
     */
    public function subscription(string $name = 'default'): ?PaystackSubscription
    {
        return $this->subscriptions()->where('name', $name)->latest()->first();
    }

    /**
     * Determine if the subscription is on trial.
     */
    public function onTrial(string $name = 'default', ?string $plan = null): bool
    {
        if (func_num_args() === 0 && empty($name)) {
            $name = 'default';
        }

        $subscription = $this->subscription($name);

        if (! $subscription || ! $subscription->onTrial()) {
            return false;
        }

        return $plan ? $subscription->hasPlan($plan) : true;
    }

    /**
     * Set the trial period for the subscription.
     */
    public function setTrial(string $name, int $days): void
    {
        if ($subscription = $this->subscription($name)) {
            $subscription->setTrial($days);
        }
    }
}
