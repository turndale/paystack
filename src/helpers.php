<?php

use Turndale\Paystack\PaystackService;

if (!function_exists('paystack')) {
    /**
     * Get the Paystack service instance
     *
     * @param string|null $method
     * @param mixed ...$args
     * @return PaystackService|mixed
     */
    function paystack(?string $method = null, ...$args)
    {
        $service = app(PaystackService::class);
        
        if (is_null($method)) {
            return $service;
        }

        // This ensures the method exists on the service before calling it
        return call_user_func_array([$service, $method], $args);
    }
}

