<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use StephenAsare\Paystack\Facades\Paystack;
use PHPUnit\Framework\Attributes\Test;

class TransactionResourceTest extends PaystackTestCase
{
    #[Test]
    public function it_can_initialize_a_transaction()
    {
        // 1. Arrange: Mock the specific Paystack endpoint
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/tp689abc',
                    'access_code' => 'tp689abc',
                    'reference' => 'test-ref-123'
                ]
            ], 200)
        ]);

        // 2. Act: Call your resource method via the facade (or direct instantiation)
        $response = Paystack::transaction()->initialize([
            'email' => 'user@example.com',
            'amount' => 10000,
        ]);

        // 3. Assert: Check the structure and values
        $this->assertTrue($response['status']);
        $this->assertEquals('test-ref-123', $response['data']['reference']);
        
        // Verification: Ensure the metadata was correctly encoded if passed
        Http::assertSent(function ($request) {
            return $request['email'] === 'user@example.com' && 
                   $request->url() === 'https://api.paystack.co/transaction/initialize';
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_verify_a_transaction()
    {
        $reference = 'test-ref-123';

        Http::fake([
            "api.paystack.co/transaction/verify/{$reference}" => Http::response([
                'status' => true,
                'data' => [
                    'id' => 12345,
                    'status' => 'success',
                    'reference' => $reference,
                    'amount' => 10000
                ]
            ], 200)
        ]);

        $response = Paystack::transaction()->verify($reference);

        $this->assertEquals('success', $response['data']['status']);
        $this->assertEquals($reference, $response['data']['reference']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_transactions()
    {
        Http::fake([
            'api.paystack.co/transaction*' => Http::response([
                'status' => true,
                'data' => [
                    ['id' => 1, 'reference' => 'ref-1'],
                    ['id' => 2, 'reference' => 'ref-2'],
                ],
                'meta' => ['total' => 2]
            ], 200)
        ]);

        $response = Paystack::transaction()->list(['perPage' => 2]);

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['meta']['total']);
    }


    #[Test]
    public function it_throws_an_exception_on_api_error()
    {
        Http::fake([
            'api.paystack.co/*' => Http::response([
                'status' => false,
                'message' => 'Invalid key'
            ], 401)
        ]);

        $this->expectException(\StephenAsare\Paystack\Exceptions\PaystackException::class);

        Paystack::transaction()->initialize(['email' => 'test@test.com', 'amount' => 100]);
    }
}