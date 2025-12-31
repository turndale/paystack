<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Facades\Paystack;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class RefundResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_initiate_a_refund()
    {
        Http::fake([
            'api.paystack.co/refund' => Http::response([
                'status' => true,
                'message' => 'Refund has been initiated',
                'data' => [
                    'id' => 12345,
                    'transaction' => 'TRX_abc123',
                    'amount' => 5000
                ]
            ], 200)
        ]);

        $response = Paystack::refund()->create([
            'transaction' => 'TRX_abc123',
            'amount' => '5000',
            'customer_note' => 'Returning damaged item'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals(12345, $response['data']['id']);

        Http::assertSent(function ($request) {
            return $request->method() === 'POST' &&
                $request['amount'] === 5000 &&
                $request['transaction'] === 'TRX_abc123';
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_retry_a_refund_with_customer_details()
    {
        $refundId = 12345;
        Http::fake([
            "api.paystack.co/refund/retry_with_customer_details/$refundId" => Http::response([
                'status' => true,
                'message' => 'Refund is being retried'
            ], 200)
        ]);

        $accountDetails = [
            'currency' => 'GHS',
            'account_number' => '0123456789',
            'bank_id' => '058'
        ];

        $response = Paystack::refund()->retry($refundId, $accountDetails);

        $this->assertTrue($response['status']);

        Http::assertSent(function ($request) use ($accountDetails) {
            return $request['refund_account_details'] === $accountDetails;
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_refunds()
    {
        Http::fake([
            'api.paystack.co/refund*' => Http::response([
                'status' => true,
                'data' => [['id' => 1], ['id' => 2]]
            ], 200)
        ]);

        $response = Paystack::refund()->list(['currency' => 'GHS']);

        $this->assertCount(2, $response['data']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'currency=GHS'));
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_a_single_refund()
    {
        $refundId = 12345;
        Http::fake([
            "api.paystack.co/refund/$refundId" => Http::response([
                'status' => true,
                'data' => ['id' => $refundId]
            ], 200)
        ]);

        $response = Paystack::refund()->fetch($refundId);

        $this->assertTrue($response['status']);
        $this->assertEquals($refundId, $response['data']['id']);
    }
}