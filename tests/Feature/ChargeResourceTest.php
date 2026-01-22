<?php

namespace Turndale\Paystack\Tests\Feature;

use Turndale\Paystack\Exceptions\PaystackException;
use Turndale\Paystack\Facades\Paystack;
use Turndale\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class ChargeResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_create_a_charge()
    {
        Http::fake([
            'api.paystack.co/charge' => Http::response([
                'status' => true,
                'message' => 'Charge attempted',
                'data' => [
                    'reference' => 'ref_123',
                    'status' => 'send_otp',
                    'display_text' => 'Please enter OTP'
                ]
            ], 200)
        ]);

        $response = Paystack::charge()->create([
            'email' => 'customer@example.com',
            'amount' => '5000', // Testing string-to-int cast
            'bank' => ['code' => '057', 'account_number' => '0000000000']
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('send_otp', $response['data']['status']);

        Http::assertSent(fn ($request) =>
            $request['amount'] === 5000 &&
            $request->method() === 'POST'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_submit_pin()
    {
        Http::fake([
            'api.paystack.co/charge/submit_pin' => Http::response([
                'status' => true,
                'data' => ['status' => 'success']
            ], 200)
        ]);

        $response = Paystack::charge()->submitPin('1234', 'ref_123');

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request['pin'] === '1234' &&
            $request['reference'] === 'ref_123'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_submit_otp()
    {
        Http::fake([
            'api.paystack.co/charge/submit_otp' => Http::response([
                'status' => true,
                'data' => ['status' => 'success']
            ], 200)
        ]);

        $response = Paystack::charge()->submitOtp('123456', 'ref_123');

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request['otp'] === '123456' &&
            $request['reference'] === 'ref_123'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_submit_phone()
    {
        Http::fake([
            'api.paystack.co/charge/submit_phone' => Http::response([
                'status' => true,
                'data' => ['status' => 'success']
            ], 200)
        ]);

        $response = Paystack::charge()->submitPhone('08012345678', 'ref_123');

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request['phone'] === '08012345678' &&
            $request['reference'] === 'ref_123'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_submit_birthday()
    {
        Http::fake([
            'api.paystack.co/charge/submit_birthday' => Http::response([
                'status' => true,
                'data' => ['status' => 'success']
            ], 200)
        ]);

        $response = Paystack::charge()->submitBirthday('1990-01-01', 'ref_123');

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request['birthday'] === '1990-01-01' &&
            $request['reference'] === 'ref_123'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_submit_address()
    {
        Http::fake([
            'api.paystack.co/charge/submit_address' => Http::response([
                'status' => true,
                'data' => ['status' => 'success']
            ], 200)
        ]);

        $payload = [
            'address' => '123 Paystack Street',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'zip_code' => '100001',
            'reference' => 'ref_123'
        ];

        $response = Paystack::charge()->submitAddress($payload);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request['address'] === '123 Paystack Street');
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_check_charge_status()
    {
        $reference = 'ref_123';
        Http::fake([
            "api.paystack.co/charge/$reference" => Http::response([
                'status' => true,
                'data' => ['status' => 'success', 'reference' => $reference]
            ], 200)
        ]);

        $response = Paystack::charge()->checkStatus($reference);

        $this->assertTrue($response['status']);
        $this->assertEquals('success', $response['data']['status']);
    }
}