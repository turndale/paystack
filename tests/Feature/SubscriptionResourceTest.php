<?php

namespace HelloFromSteve\Paystack\Tests\Feature;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use HelloFromSteve\Paystack\Facades\Paystack;
use HelloFromSteve\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class SubscriptionResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_create_a_subscription()
    {
        Http::fake([
            'api.paystack.co/subscription' => Http::response([
                'status' => true,
                'message' => 'Subscription created successfully',
                'data' => [
                    'customer' => 12345,
                    'plan' => 'PLN_pro_123',
                    'subscription_code' => 'SUB_abc123'
                ]
            ], 200)
        ]);

        $response = Paystack::subscription()->create([
            'customer' => 'stephen@stephenasare.dev',
            'plan' => 'PLN_pro_123'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('SUB_abc123', $response['data']['subscription_code']);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://api.paystack.co/subscription' &&
            $request->method() === 'POST'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_disable_a_subscription()
    {
        Http::fake([
            'api.paystack.co/subscription/disable' => Http::response([
                'status' => true,
                'message' => 'Subscription disabled successfully'
            ], 200)
        ]);

        $response = Paystack::subscription()->disable('SUB_abc123', 'token_123');

        $this->assertTrue($response['status']);
        $this->assertEquals('Subscription disabled successfully', $response['message']);

        Http::assertSent(fn ($request) =>
            $request['code'] === 'SUB_abc123' &&
            $request['token'] === 'token_123'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_get_a_subscription_update_link()
    {
        $code = 'SUB_abc123';
        Http::fake([
            "api.paystack.co/subscription/$code/manage/link" => Http::response([
                'status' => true,
                'data' => [
                    'link' => 'https://paystack.com/manage/sub_xyz'
                ]
            ], 200)
        ]);

        $link = Paystack::subscription()->getUpdateLink($code);

        $this->assertIsString($link);
        $this->assertEquals('https://paystack.com/manage/sub_xyz', $link);

        Http::assertSent(fn ($request) =>
            $request->url() === "https://api.paystack.co/subscription/$code/manage/link" &&
            $request->method() === 'GET'
        );
    }
}