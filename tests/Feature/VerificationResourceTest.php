<?php

namespace Turndale\Paystack\Tests\Feature;

use Turndale\Paystack\Exceptions\PaystackException;
use Turndale\Paystack\Facades\Paystack;
use Turndale\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class VerificationResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_resolve_a_bank_account()
    {
        Http::fake([
            'api.paystack.co/bank/resolve*' => Http::response([
                'status' => true,
                'message' => 'Account number resolved',
                'data' => [
                    'account_number' => '0123456789',
                    'account_name' => 'STEPHEN ASARE'
                ]
            ], 200)
        ]);

        $response = Paystack::verification()->resolveAccount('0123456789', '058');

        $this->assertTrue($response['status']);
        $this->assertEquals('STEPHEN ASARE', $response['data']['account_name']);

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://api.paystack.co/bank/resolve?account_number=0123456789&bank_code=058'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_validate_account_details_for_kyc()
    {
        Http::fake([
            'api.paystack.co/bank/validate' => Http::response([
                'status' => true,
                'message' => 'Personal Details Validated'
            ], 200)
        ]);

        $payload = [
            'account_name' => 'Stephen Asare',
            'account_number' => '0123456789',
            'bank_code' => '058',
            'country_code' => 'NG'
        ];

        $response = Paystack::verification()->validateAccount($payload);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request->method() === 'POST' &&
            $request['account_name'] === 'Stephen Asare'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_resolve_card_bin_information()
    {
        $bin = '539983';
        Http::fake([
            "api.paystack.co/decision/bin/$bin" => Http::response([
                'status' => true,
                'data' => [
                    'bin' => $bin,
                    'brand' => 'Mastercard',
                    'card_type' => 'DEBIT',
                    'bank' => 'Guaranty Trust Bank'
                ]
            ], 200)
        ]);

        $response = Paystack::verification()->resolveCardBin($bin);

        $this->assertTrue($response['status']);
        $this->assertEquals('Mastercard', $response['data']['brand']);
        $this->assertEquals('DEBIT', $response['data']['card_type']);

        Http::assertSent(fn ($request) =>
            $request->url() === "https://api.paystack.co/decision/bin/$bin"
        );
    }
}