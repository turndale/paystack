<?php

namespace HelloFromSteve\Paystack\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaystackTransaction extends Model
{
    protected $guarded = [];

    /**
     * Get the parent billable model.
     */
    public function billable(): MorphTo
    {
        return $this->morphTo();
    }
}