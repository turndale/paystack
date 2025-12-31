<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Facades\Paystack;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class TransferRecipientResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_create_a_transfer_recipient()
    {
        Http::fake([
            'api.paystack.co/transferrecipient' => Http::response([
                'status' => true,
                'message' => 'Recipient created',
                'data' => [
                    'recipient_code' => 'RCP_12345',
                    'name' => 'Stephen Asare',
                    'type' => 'nuban'
                ]
            ], 201)
        ]);

        $response = Paystack::transferRecipient()->create([
            'type' => 'nuban',
            'name' => 'Stephen Asare',
            'account_number' => '0123456789',
            'bank_code' => '058',
            'currency' => 'NGN'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('RCP_12345', $response['data']['recipient_code']);

        Http::assertSent(fn ($request) => $request->method() === 'POST');
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_bulk_create_recipients()
    {
        Http::fake([
            'api.paystack.co/transferrecipient/bulk' => Http::response([
                'status' => true,
                'message' => 'Recipients created'
            ], 200)
        ]);

        $batch = [
            ['name' => 'Stephen Asare', 'account_number' => '0123456789', 'bank_code' => '058'],
            ['name' => 'Princess Yankson', 'account_number' => '9876543210', 'bank_code' => '011']
        ];

        $response = Paystack::transferRecipient()->bulkCreate($batch);

        $this->assertTrue($response['status']);

        Http::assertSent(function ($request) use ($batch) {
            return $request['batch'] === $batch &&
                $request->url() === 'https://api.paystack.co/transferrecipient/bulk';
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_recipients()
    {
        Http::fake([
            'api.paystack.co/transferrecipient*' => Http::response([
                'status' => true,
                'data' => [['recipient_code' => 'RCP_1'], ['recipient_code' => 'RCP_2']]
            ], 200)
        ]);

        $response = Paystack::transferRecipient()->list(['perPage' => 2]);

        $this->assertCount(2, $response['data']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'perPage=2'));
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_a_recipient()
    {
        $code = 'RCP_12345';
        Http::fake([
            "api.paystack.co/transferrecipient/$code" => Http::response([
                'status' => true,
                'data' => ['recipient_code' => $code]
            ], 200)
        ]);

        $response = Paystack::transferRecipient()->fetch($code);

        $this->assertEquals($code, $response['data']['recipient_code']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_update_a_recipient()
    {
        $code = 'RCP_12345';
        Http::fake([
            "api.paystack.co/transferrecipient/$code" => Http::response([
                'status' => true,
                'message' => 'Recipient updated'
            ], 200)
        ]);

        $response = Paystack::transferRecipient()->update($code, ['name' => 'Stephen']);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request->method() === 'PUT');
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_delete_a_recipient()
    {
        $code = 'RCP_12345';
        Http::fake([
            "api.paystack.co/transferrecipient/$code" => Http::response([
                'status' => true,
                'message' => 'Recipient deleted'
            ], 200)
        ]);

        $response = Paystack::transferRecipient()->delete($code);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request->method() === 'DELETE');
    }
}