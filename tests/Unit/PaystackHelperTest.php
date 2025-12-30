<?php

namespace HelloFromSteve\Paystack\Tests\Unit;

use HelloFromSteve\Paystack\PaystackService;
use HelloFromSteve\Paystack\Resources\TransactionResource;
use HelloFromSteve\Paystack\Tests\PaystackTestCase;
use PHPUnit\Framework\Attributes\Test;

class PaystackHelperTest extends PaystackTestCase
{
    #[Test]
    public function the_paystack_helper_returns_the_service_instance()
    {
        // 1. Act: Call the helper
        $result = paystack();

        // 2. Assert: Check it is the right class
        $this->assertInstanceOf(PaystackService::class, $result);
    }

    #[Test]
    public function the_paystack_helper_can_access_resources()
    {
        // 1. Act & Assert: Chain the helper to a resource
        $this->assertInstanceOf(TransactionResource::class, paystack()->transaction());
    }

    #[Test]
    public function calling_the_helper_multiple_times_returns_the_same_instance()
    {
        $instanceOne = paystack();
        $instanceTwo = paystack();

        $this->assertSame($instanceOne, $instanceTwo);
    }
}