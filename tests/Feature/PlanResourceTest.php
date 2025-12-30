<?php

namespace HelloFromSteve\Paystack\Tests\Feature;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use HelloFromSteve\Paystack\Facades\Paystack;
use HelloFromSteve\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class PlanResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_create_a_plan()
    {
        Http::fake([
            'api.paystack.co/plan' => Http::response([
                'status' => true,
                'message' => 'Plan created',
                'data' => [
                    'name' => 'Monthly Pro',
                    'plan_code' => 'PLN_pro_123',
                    'amount' => 5000,
                    'interval' => 'monthly'
                ]
            ], 201)
        ]);

        $response = Paystack::plan()->create([
            'name' => 'Monthly Pro',
            'amount' => '5000',
            'interval' => 'monthly'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('PLN_pro_123', $response['data']['plan_code']);

        Http::assertSent(function ($request) {
            return $request['amount'] === 5000 &&
                $request['interval'] === 'monthly' &&
                $request->method() === 'POST';
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_all_plans()
    {
        Http::fake([
            'api.paystack.co/plan*' => Http::response([
                'status' => true,
                'data' => [
                    ['name' => 'Basic', 'plan_code' => 'PLN_1'],
                    ['name' => 'Premium', 'plan_code' => 'PLN_2']
                ]
            ], 200)
        ]);

        $response = Paystack::plan()->list(['interval' => 'monthly']);

        $this->assertCount(2, $response['data']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'interval=monthly'));
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_a_plan_by_id_or_code()
    {
        $planCode = 'PLN_pro_123';
        Http::fake([
            "api.paystack.co/plan/$planCode" => Http::response([
                'status' => true,
                'data' => ['plan_code' => $planCode]
            ], 200)
        ]);

        $response = Paystack::plan()->fetch($planCode);

        $this->assertEquals($planCode, $response['data']['plan_code']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_update_a_plan()
    {
        $planCode = 'PLN_pro_123';
        Http::fake([
            "api.paystack.co/plan/$planCode" => Http::response([
                'status' => true,
                'message' => 'Plan updated'
            ], 200)
        ]);

        $response = Paystack::plan()->update($planCode, [
            'name' => 'Pro Yearly',
            'amount' => 50000
        ]);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request->method() === 'PUT' &&
            $request['amount'] === 50000
        );
    }
}