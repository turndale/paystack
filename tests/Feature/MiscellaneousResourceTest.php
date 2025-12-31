<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Facades\Paystack;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class MiscellaneousResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_banks()
    {
        Http::fake([
            'api.paystack.co/bank*' => Http::response([
                'status' => true,
                'message' => 'Banks retrieved',
                'data' => [
                    ['name' => 'Access Bank', 'code' => '044'],
                    ['name' => 'GTBank', 'code' => '058']
                ]
            ], 200)
        ]);

        $response = Paystack::miscellaneous()->listBanks(['country' => 'ghana']);

        $this->assertTrue($response['status']);
        $this->assertCount(2, $response['data']);
        $this->assertEquals('Access Bank', $response['data'][0]['name']);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://api.paystack.co/bank?country=ghana'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_supported_countries()
    {
        Http::fake([
            'api.paystack.co/country' => Http::response([
                'status' => true,
                'data' => [
                    ['name' => 'Ghana', 'iso_code' => 'GH'],
                    ['name' => 'Nigeria', 'iso_code' => 'NG']
                ]
            ], 200)
        ]);

        $response = Paystack::miscellaneous()->listCountries();

        $this->assertTrue($response['status']);
        $this->assertEquals('GH', $response['data'][0]['iso_code']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_states_for_address_verification()
    {
        $countryCode = 'GH';
        Http::fake([
            'api.paystack.co/address_verification/states*' => Http::response([
                'status' => true,
                'data' => [
                    ['name' => 'Accra', 'abbreviation' => 'AC'],
                    ['name' => 'Kumasi', 'abbreviation' => 'KS']
                ]
            ], 200)
        ]);

        $response = Paystack::miscellaneous()->listStates($countryCode);

        $this->assertTrue($response['status']);
        $this->assertEquals('AC', $response['data'][0]['abbreviation']);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://api.paystack.co/address_verification/states?country=GH'
        );
    }
}