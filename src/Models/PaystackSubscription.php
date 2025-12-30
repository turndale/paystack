<?php

namespace HelloFromSteve\Paystack\Models;

use Illuminate\Database\Eloquent\Model;

class PaystackSubscription extends Model
{
    protected $guarded = [];
    protected $table = 'paystack_subscriptions';

    protected $casts = [
        'ends_at' => 'datetime',
    ];

    public function billable()
    {
        return $this->morphTo();
    }

    public function active(): bool
    {
        return $this->paystack_status === 'active' && 
               (is_null($this->ends_at) || $this->ends_at->isFuture());
    }


    public function onGracePeriod(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    public function cancelled(): bool
    {
        return ! is_null($this->ends_at);
    }
}