<?php

namespace StephenAsare\Paystack\Concerns;

use StephenAsare\Paystack\Models\PaystackTransaction;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ManagesTransactions
{
    /**
     * Get the transactions relationship.
     */
    public function paystackTransactions(): MorphMany
    {
        return $this->morphMany(PaystackTransaction::class, 'billable');
    }
}
