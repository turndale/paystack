<?php

namespace Turndale\Paystack\Tests\Feature;

use Turndale\Paystack\Exceptions\PaystackException;
use Turndale\Paystack\Facades\Paystack;
use Turndale\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class PaymentRequestResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_create_a_payment_request()
    {
        Http::fake([
            'api.paystack.co/paymentrequest' => Http::response([
                'status' => true,
                'message' => 'Payment request created',
                'data' => ['id' => 123, 'payment_request_code' => 'PRQ_xyz']
            ], 200)
        ]);

        $response = Paystack::paymentRequest()->create([
            'customer' => 'CUS_123',
            'amount' => '15000',
            'description' => 'Invoice for services'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('PRQ_xyz', $response['data']['payment_request_code']);

        Http::assertSent(fn ($request) => $request['amount'] === 15000);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_verify_a_payment_request()
    {
        $code = 'PRQ_xyz';
        Http::fake([
            "api.paystack.co/paymentrequest/verify/$code" => Http::response([
                'status' => true,
                'message' => 'Payment request retrieved'
            ], 200)
        ]);

        $response = Paystack::paymentRequest()->verify($code);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request->url() === "https://api.paystack.co/paymentrequest/verify/$code");
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_send_a_notification()
    {
        $code = 'PRQ_xyz';
        Http::fake([
            "api.paystack.co/paymentrequest/notify/$code" => Http::response([
                'status' => true,
                'message' => 'Notification sent'
            ], 200)
        ]);

        $response = Paystack::paymentRequest()->notify($code);

        $this->assertTrue($response['status']);
        $this->assertEquals('Notification sent', $response['message']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_totals_metrics()
    {
        Http::fake([
            'api.paystack.co/paymentrequest/totals' => Http::response([
                'status' => true,
                'data' => ['total_pending' => 5000]
            ], 200)
        ]);

        $response = Paystack::paymentRequest()->totals();

        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('total_pending', $response['data']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_finalize_a_draft_request()
    {
        $code = 'PRQ_xyz';
        Http::fake([
            "api.paystack.co/paymentrequest/finalize/$code" => Http::response([
                'status' => true,
                'message' => 'Payment request finalized'
            ], 200)
        ]);

        $response = Paystack::paymentRequest()->finalize($code, false);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request['send_notification'] === false);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_archive_a_payment_request()
    {
        $code = 'PRQ_xyz';
        Http::fake([
            "api.paystack.co/paymentrequest/archive/$code" => Http::response([
                'status' => true,
                'message' => 'Payment request archived'
            ], 200)
        ]);

        $response = Paystack::paymentRequest()->archive($code);

        $this->assertTrue($response['status']);
        $this->assertEquals('Payment request archived', $response['message']);
    }
}