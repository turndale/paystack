<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Facades\Paystack;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class TransactionSplitResourceTest extends PaystackTestCase
{
    #[Test]
    public function it_can_create_a_split()
    {
        Http::fake([
            'api.paystack.co/split' => Http::response([
                'status' => true,
                'message' => 'Split created',
                'data' => ['id' => 123, 'name' => 'Test Split']
            ], 200)
        ]);

        $response = Paystack::transactionSplit()->create([
            'name' => 'Test Split',
            'type' => 'percentage',
            'currency' => 'NGN',
            'subaccounts' => [['subaccount' => 'ACCT_xxxx', 'share' => 20]]
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('Test Split', $response['data']['name']);
    }

    #[Test]
    public function it_can_list_splits()
    {
        Http::fake([
            'api.paystack.co/split*' => Http::response([
                'status' => true,
                'data' => [['id' => 123, 'name' => 'Test Split']]
            ], 200)
        ]);

        $response = Paystack::transactionSplit()->list();

        $this->assertTrue($response['status']);
        $this->assertCount(1, $response['data']);
    }

    #[Test]
    public function it_can_fetch_a_split()
    {
        Http::fake([
            'api.paystack.co/split/123' => Http::response([
                'status' => true,
                'data' => ['id' => 123, 'name' => 'Test Split']
            ], 200)
        ]);

        $response = Paystack::transactionSplit()->fetch('123');

        $this->assertTrue($response['status']);
        $this->assertEquals(123, $response['data']['id']);
    }

    #[Test]
    public function it_can_update_a_split()
    {
        Http::fake([
            'api.paystack.co/split/123' => Http::response([
                'status' => true,
                'message' => 'Split updated',
                'data' => ['id' => 123, 'name' => 'Updated Split']
            ], 200)
        ]);

        $response = Paystack::transactionSplit()->update('123', ['name' => 'Updated Split']);

        $this->assertTrue($response['status']);
        $this->assertEquals('Updated Split', $response['data']['name']);
    }

    #[Test]
    public function it_can_add_subaccount_to_split()
    {
        Http::fake([
            'api.paystack.co/split/123/subaccount/add' => Http::response([
                'status' => true,
                'message' => 'Subaccount added',
                'data' => ['id' => 123]
            ], 200)
        ]);

        $response = Paystack::transactionSplit()->addSubaccount('123', ['subaccount' => 'ACCT_xxxx', 'share' => 20]);

        $this->assertTrue($response['status']);
    }

    #[Test]
    public function it_can_remove_subaccount_from_split()
    {
        Http::fake([
            'api.paystack.co/split/123/subaccount/remove' => Http::response([
                'status' => true,
                'message' => 'Subaccount removed',
                'data' => ['id' => 123]
            ], 200)
        ]);

        $response = Paystack::transactionSplit()->removeSubaccount('123', 'ACCT_xxxx');

        $this->assertTrue($response['status']);
    }
}
