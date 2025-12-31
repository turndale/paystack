<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Facades\Paystack;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class SettlementResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_settlements()
    {
        Http::fake([
            'api.paystack.co/settlement*' => Http::response([
                'status' => true,
                'message' => 'Settlements retrieved',
                'data' => [
                    ['id' => 123, 'status' => 'success', 'total_amount' => 50000],
                    ['id' => 124, 'status' => 'processing', 'total_amount' => 25000]
                ]
            ], 200)
        ]);

        $response = Paystack::settlement()->list(['status' => 'success']);

        $this->assertTrue($response['status']);
        $this->assertCount(2, $response['data']);
        $this->assertEquals(123, $response['data'][0]['id']);

        Http::assertSent(fn ($request) =>
        str_contains($request->url(), 'status=success')
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_transactions_for_a_specific_settlement()
    {
        $settlementId = '778899';

        Http::fake([
            "api.paystack.co/settlement/$settlementId/transactions*" => Http::response([
                'status' => true,
                'message' => 'Transactions retrieved',
                'data' => [
                    ['id' => 1, 'amount' => 10000, 'reference' => 'ref_001'],
                    ['id' => 2, 'amount' => 15000, 'reference' => 'ref_002']
                ]
            ], 200)
        ]);

        $response = Paystack::settlement()->transactions($settlementId, ['perPage' => 1]);

        $this->assertTrue($response['status']);
        $this->assertCount(2, $response['data']);

        Http::assertSent(function ($request) use ($settlementId) {
            return $request->url() === "https://api.paystack.co/settlement/$settlementId/transactions?perPage=1" &&
                $request->method() === 'GET';
        });
    }
}