<?php

namespace StephenAsare\Paystack\Models;

use Illuminate\Database\Eloquent\Model;

class PaystackSubscription extends Model
{
    protected $guarded = [];
    protected $table = 'paystack_subscriptions';

    protected $casts = [
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
    ];

    public function billable()
    {
        return $this->morphTo();
    }

    public function active(): bool
    {
        return ($this->paystack_status === 'active' || $this->onGracePeriod() || $this->onTrial()) && 
               (is_null($this->ends_at) || $this->ends_at->isFuture());
    }

    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function setTrial(int $days): self
    {
        $this->forceFill([
            'paystack_status' => 'trial',
            'trial_ends_at' => now()->addDays($days),
        ])->save();

        return $this;
    }


    public function onGracePeriod(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }

    public function hasPlan(string $plan): bool
    {
        return $this->paystack_plan === $plan;
    }

    public function cancelled(): bool
    {
        return ! is_null($this->ends_at);
    }
}