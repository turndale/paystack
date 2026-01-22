<?php

namespace Turndale\Paystack\Tests\Feature;

use Turndale\Paystack\Events\PaymentSuccess;
use Turndale\Paystack\Events\SubscriptionCreated;
use Turndale\Paystack\Events\WebhookHandled;
use Turndale\Paystack\Events\WebhookReceived;
use Turndale\Paystack\Tests\PaystackTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;

class WebhookTest extends PaystackTestCase
{
    use RefreshDatabase;

    public function test_it_can_handle_webhook_and_dispatch_events()
    {
        Event::fake();
        
        // Ensure secret matches what we sign with
        Config::set('paystack.secret', 'sk_test_mock_123');

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'ref_123',
                'amount' => 10000,
            ]
        ];

        $signature = hash_hmac('sha512', json_encode($payload), 'sk_test_mock_123');

        $response = $this->postJson('paystack/webhook', $payload, [
            'x-paystack-signature' => $signature
        ]);

        $response->assertOk();
        
        Event::assertDispatched(WebhookReceived::class);
        Event::assertDispatched(PaymentSuccess::class);
        Event::assertDispatched(WebhookHandled::class);
    }

    public function test_it_rejects_invalid_signature()
    {
        Config::set('paystack.secret', 'sk_test_mock_123');

        $payload = ['event' => 'charge.success'];
        
        $response = $this->postJson('paystack/webhook', $payload, [
            'x-paystack-signature' => 'wrong_signature'
        ]);

        $response->assertStatus(401);
    }

    public function test_it_handles_subscription_created()
    {
        Event::fake();
        Config::set('paystack.secret', 'sk_test_mock_123');

        $payload = [
            'event' => 'subscription.create',
            'data' => [
                'subscription_code' => 'SUB_123',
                'status' => 'active'
            ]
        ];

        $signature = hash_hmac('sha512', json_encode($payload), 'sk_test_mock_123');

        $this->postJson('paystack/webhook', $payload, [
            'x-paystack-signature' => $signature
        ])->assertOk();

        Event::assertDispatched(SubscriptionCreated::class);
    }
}
