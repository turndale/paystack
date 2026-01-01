<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Facades\Paystack;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class SubaccountResourceTest extends PaystackTestCase
{
    #[Test]
    public function it_can_create_a_subaccount()
    {
        Http::fake([
            'api.paystack.co/subaccount' => Http::response([
                'status' => true,
                'message' => 'Subaccount created',
                'data' => ['subaccount_code' => 'ACCT_xxxx', 'business_name' => 'Test Biz']
            ], 200)
        ]);

        $response = Paystack::subaccount()->create([
            'business_name' => 'Test Biz',
            'settlement_bank' => '044',
            'account_number' => '0193274682',
            'percentage_charge' => 18.2
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('ACCT_xxxx', $response['data']['subaccount_code']);
    }

    #[Test]
    public function it_can_list_subaccounts()
    {
        Http::fake([
            'api.paystack.co/subaccount*' => Http::response([
                'status' => true,
                'data' => [['subaccount_code' => 'ACCT_xxxx']]
            ], 200)
        ]);

        $response = Paystack::subaccount()->list();

        $this->assertTrue($response['status']);
        $this->assertCount(1, $response['data']);
    }

    #[Test]
    public function it_can_fetch_a_subaccount()
    {
        Http::fake([
            'api.paystack.co/subaccount/ACCT_xxxx' => Http::response([
                'status' => true,
                'data' => ['subaccount_code' => 'ACCT_xxxx']
            ], 200)
        ]);

        $response = Paystack::subaccount()->fetch('ACCT_xxxx');

        $this->assertTrue($response['status']);
        $this->assertEquals('ACCT_xxxx', $response['data']['subaccount_code']);
    }

    #[Test]
    public function it_can_update_a_subaccount()
    {
        Http::fake([
            'api.paystack.co/subaccount/ACCT_xxxx' => Http::response([
                'status' => true,
                'message' => 'Subaccount updated',
                'data' => ['subaccount_code' => 'ACCT_xxxx', 'business_name' => 'Updated Biz']
            ], 200)
        ]);

        $response = Paystack::subaccount()->update('ACCT_xxxx', ['business_name' => 'Updated Biz']);

        $this->assertTrue($response['status']);
        $this->assertEquals('Updated Biz', $response['data']['business_name']);
    }
}
