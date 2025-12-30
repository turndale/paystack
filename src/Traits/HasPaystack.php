<?php


namespace HelloFromSteve\Paystack\Traits;

use HelloFromSteve\Paystack\Models\PaystackSubscription;
use HelloFromSteve\Paystack\Models\PaystackCustomer;
use HelloFromSteve\Paystack\Models\PaystackTransaction;

trait HasPaystack
{
    public function paystackCustomer() {
        return $this->morphOne(PaystackCustomer::class, 'billable');
    }

    public function subscriptions() {
        return $this->morphMany(PaystackSubscription::class, 'billable');
    }

    public function paystackTransactions() {
        return $this->morphMany(PaystackTransaction::class, 'billable');
    }

    /**
     * Check if currently subscribed
     */
    public function subscribed(string $name = 'default'): bool
    {
        $subscription = $this->subscriptions()
            ->where('name', $name)
            ->first();

        return $subscription ? $subscription->active() : false;
    }

    /**
     * Fluent method to register a new subscription
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
     * Find the billable model by Paystack Customer ID.
     */
    public static function findByPaystackId(string $id)
    {
        return (new PaystackCustomer())->where('paystack_id', $id)->first()?->billable;
    }
}