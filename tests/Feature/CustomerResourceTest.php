<?php

namespace Turndale\Paystack\Tests\Feature;

use Turndale\Paystack\Exceptions\PaystackException;
use Turndale\Paystack\Facades\Paystack;
use Turndale\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class CustomerResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_create_a_customer()
    {
        // 1. Arrange
        Http::fake([
            'api.paystack.co/customer' => Http::response([
                'status' => true,
                'message' => 'Customer created',
                'data' => [
                    'email' => 'stephen@stephenasare.dev',
                    'customer_code' => 'CUS_12345',
                    'id' => 98765
                ]
            ], 200)
        ]);

        // 2. Act
        $response = Paystack::customer()->create([
            'email' => 'stephen@stephenasare.dev',
            'first_name' => 'Stephen',
            'last_name' => 'Asare'
        ]);

        // 3. Assert
        $this->assertTrue($response['status']);
        $this->assertEquals('CUS_12345', $response['data']['customer_code']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.paystack.co/customer' &&
                $request['email'] === 'stephen@stephenasare.dev';
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_a_customer_by_email_or_code()
    {
        $identifier = 'stephen@stephenasare.dev';

        // 1. Arrange
        Http::fake([
            "api.paystack.co/customer/{$identifier}" => Http::response([
                'status' => true,
                'data' => [
                    'customer_code' => 'CUS_12345',
                    'email' => $identifier
                ]
            ], 200)
        ]);

        // 2. Act
        $response = Paystack::customer()->fetch($identifier);

        // 3. Assert
        $this->assertTrue($response['status']);
        $this->assertEquals('CUS_12345', $response['data']['customer_code']);
    }


    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_update_a_customer()
    {
        $code = 'CUS_xnxdt6s1zg1f4nx';
        Http::fake([
            "api.paystack.co/customer/$code" => Http::response([
                'status' => true,
                'message' => 'Customer updated',
                'data' => ['first_name' => 'Stephen']
            ], 200)
        ]);

        $response = Paystack::customer()->update($code, ['first_name' => 'Stephen']);

        $this->assertTrue($response['status']);
        $this->assertEquals('Stephen', $response['data']['first_name']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_validate_a_customer_identification()
    {
        $code = 'CUS_customer_code';
        Http::fake([
            "api.paystack.co/customer/$code/identification" => Http::response([
                'status' => true,
                'message' => 'Customer Identification in progress'
            ], 202)
        ]);

        $response = Paystack::customer()->validate($code, [
            'country' => 'GH',
            'type' => 'bank_account',
            'account_number' => '0123456789'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('Customer Identification in progress', $response['message']);
    }


    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_set_risk_action_for_a_customer()
    {
        Http::fake([
            'api.paystack.co/customer/set_risk_action' => Http::response([
                'status' => true,
                'message' => 'Customer updated',
                'data' => ['risk_action' => 'allow']
            ], 200)
        ]);

        $response = Paystack::customer()->setRiskAction([
            'customer' => 'CUS_xr58yrr2ujlft9k',
            'risk_action' => 'allow'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('allow', $response['data']['risk_action']);
    }


    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_deactivate_an_authorization()
    {
        Http::fake([
            'api.paystack.co/customer/authorization/deactivate' => Http::response([
                'status' => true,
                'message' => 'Authorization has been deactivated'
            ], 200)
        ]);

        $response = Paystack::customer()->deactivateAuthorization('AUTH_xxxIjkZVj5');

        $this->assertTrue($response['status']);
        $this->assertEquals('Authorization has been deactivated', $response['message']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_initialize_authorization()
    {
        Http::fake([
            'api.paystack.co/customer/authorization/initialize' => Http::response([
                'status' => true,
                'data' => ['access_code' => '82t4mp5b5mfn51h']
            ], 200)
        ]);

        $response = Paystack::customer()->initializeAuthorization([
            'email' => 'stephen@stephenasare.dev',
            'channel' => 'direct_debit'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('82t4mp5b5mfn51h', $response['data']['access_code']);
    }
}