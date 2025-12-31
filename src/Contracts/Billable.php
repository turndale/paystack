<?php

namespace StephenAsare\Paystack\Contracts;

use StephenAsare\Paystack\Models\PaystackCustomer;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use StephenAsare\Paystack\SubscriptionBuilder;

interface Billable
{
    /**
     * Get the customer relationship.
     */
    public function paystackCustomer(): MorphOne;

    /**
     * Get the subscriptions relationship.
     */
    public function subscriptions(): MorphMany;

    /**
     * Get the transactions relationship.
     */
    public function paystackTransactions(): MorphMany;

    /**
     * Create a Paystack customer for the billable model.
     *
     * @param array $options
     * @return PaystackCustomer
     */
    public function createAsPaystackCustomer(array $options = []);

    /**
     * Check if currently subscribed.
     *
     * @param string $name
     * @return bool
     */
    public function subscribed(string $name = 'default'): bool;

    /**
     * Begin creating a new subscription.
     *
     * @param string $name
     * @param string $plan
     * @return SubscriptionBuilder
     */
    public function newSubscription(string $name, string $plan): SubscriptionBuilder;
}
