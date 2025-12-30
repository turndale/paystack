<?php

namespace HelloFromSteve\Paystack\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaystackCustomer extends Model
{
   protected $table = 'paystack_customers';
    protected $guarded = [];

    public function billable(): MorphTo
    {
        return $this->morphTo();
    }


    /**
     * Find the billable model associated with a Paystack Customer Code.
     */
    public static function findBillable(string $paystackCustomerId)
    {
        return self::where('paystack_id', $paystackCustomerId)->first()?->billable;
    }
}