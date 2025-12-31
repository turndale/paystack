<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Facades\Paystack;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class TransferResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_initiate_a_transfer()
    {
        Http::fake([
            'api.paystack.co/transfer' => Http::response([
                'status' => true,
                'message' => 'Transfer has been queued',
                'data' => [
                    'transfer_code' => 'TRF_12345',
                    'amount' => 10000,
                    'status' => 'pending'
                ]
            ], 200)
        ]);

        $response = Paystack::transfer()->initiate([
            'amount' => '10000',
            'recipient' => 'RCP_recipient_code',
            'reason' => 'Monthly Salary'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('TRF_12345', $response['data']['transfer_code']);

        Http::assertSent(function ($request) {
            return $request['amount'] === 10000 &&
                $request['source'] === 'balance' && // Testing default value
                $request->method() === 'POST';
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_finalize_a_transfer_with_otp()
    {
        Http::fake([
            'api.paystack.co/transfer/finalize_transfer' => Http::response([
                'status' => true,
                'message' => 'Transfer finalized'
            ], 200)
        ]);

        $response = Paystack::transfer()->finalize('TRF_12345', '123456');

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request['transfer_code'] === 'TRF_12345' &&
            $request['otp'] === '123456'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_initiate_bulk_transfers()
    {
        Http::fake([
            'api.paystack.co/transfer/bulk' => Http::response([
                'status' => true,
                'message' => 'Bulk transfer initiated'
            ], 200)
        ]);

        $transfers = [
            ['amount' => 5000, 'recipient' => 'RCP_001'],
            ['amount' => 7000, 'recipient' => 'RCP_002']
        ];

        $response = Paystack::transfer()->bulk($transfers);

        $this->assertTrue($response['status']);
        Http::assertSent(function ($request) use ($transfers) {
            return $request['transfers'] === $transfers &&
                $request['source'] === 'balance' &&
                $request['currency'] === 'NGN';
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_transfers()
    {
        Http::fake([
            'api.paystack.co/transfer*' => Http::response([
                'status' => true,
                'data' => [['transfer_code' => 'TRF_1'], ['transfer_code' => 'TRF_2']]
            ], 200)
        ]);

        $response = Paystack::transfer()->list(['perPage' => 2]);

        $this->assertCount(2, $response['data']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'perPage=2'));
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_verify_a_transfer_by_reference()
    {
        $reference = 'ref_unique_123';
        Http::fake([
            "api.paystack.co/transfer/verify/$reference" => Http::response([
                'status' => true,
                'data' => ['reference' => $reference, 'status' => 'success']
            ], 200)
        ]);

        $response = Paystack::transfer()->verify($reference);

        $this->assertTrue($response['status']);
        $this->assertEquals('success', $response['data']['status']);
    }
}