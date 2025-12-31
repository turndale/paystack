<?php

namespace StephenAsare\Paystack\Traits;

use StephenAsare\Paystack\Concerns\ManagesCustomer;
use StephenAsare\Paystack\Concerns\ManagesSubscriptions;
use StephenAsare\Paystack\Concerns\ManagesTransactions;

trait HasPaystack
{
    use ManagesCustomer,
        ManagesSubscriptions,
        ManagesTransactions;
}
