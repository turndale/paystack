<?php

namespace Turndale\Paystack\Tests\Feature;

use Turndale\Paystack\Exceptions\PaystackException;
use Turndale\Paystack\Facades\Paystack;
use Turndale\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class TransferControlResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_check_balance()
    {
        Http::fake([
            'api.paystack.co/balance' => Http::response([
                'status' => true,
                'message' => 'Balances retrieved',
                'data' => [
                    ['currency' => 'NGN', 'balance' => 1000000],
                    ['currency' => 'GHS', 'balance' => 50000]
                ]
            ], 200)
        ]);

        $response = Paystack::transferControl()->checkBalance();

        $this->assertTrue($response['status']);
        $this->assertEquals('NGN', $response['data'][0]['currency']);
        $this->assertEquals(1000000, $response['data'][0]['balance']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_balance_ledger()
    {
        Http::fake([
            'api.paystack.co/balance/ledger*' => Http::response([
                'status' => true,
                'message' => 'Ledger retrieved',
                'data' => [['id' => 1, 'amount' => 5000, 'type' => 'credit']]
            ], 200)
        ]);

        $response = Paystack::transferControl()->fetchLedger(['perPage' => 1]);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'perPage=1'));
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_resend_otp_for_transfer()
    {
        Http::fake([
            'api.paystack.co/transfer/resend_otp' => Http::response([
                'status' => true,
                'message' => 'OTP has been resent'
            ], 200)
        ]);

        $response = Paystack::transferControl()->resendOtp('TRF_123', 'resend_otp');

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request['transfer_code'] === 'TRF_123' &&
            $request['reason'] === 'resend_otp'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_request_to_disable_otp()
    {
        Http::fake([
            'api.paystack.co/transfer/disable_otp' => Http::response([
                'status' => true,
                'message' => 'OTP has been sent to your phone'
            ], 200)
        ]);

        $response = Paystack::transferControl()->disableOtpRequest();

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request->method() === 'POST');
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_finalize_disabling_otp()
    {
        Http::fake([
            'api.paystack.co/transfer/disable_otp_finalize' => Http::response([
                'status' => true,
                'message' => 'OTP requirement disabled'
            ], 200)
        ]);

        $response = Paystack::transferControl()->disableOtpFinalize('123456');

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request['otp'] === '123456');
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_enable_otp_requirement()
    {
        Http::fake([
            'api.paystack.co/transfer/enable_otp' => Http::response([
                'status' => true,
                'message' => 'OTP requirement enabled'
            ], 200)
        ]);

        $response = Paystack::transferControl()->enableOtp();

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request->method() === 'POST');
    }
}