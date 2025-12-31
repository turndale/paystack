<?php

namespace StephenAsare\Paystack\Tests\Feature;

use StephenAsare\Paystack\Exceptions\PaystackException;
use StephenAsare\Paystack\Facades\Paystack;
use StephenAsare\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class ProductResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_create_a_product()
    {
        Http::fake([
            'api.paystack.co/product' => Http::response([
                'status' => true,
                'message' => 'Product created',
                'data' => [
                    'name' => 'Custom Hoodie',
                    'price' => 15000,
                    'id' => 12345,
                    'product_code' => 'PROD_xyz'
                ]
            ], 201)
        ]);

        $response = Paystack::product()->create([
            'name' => 'Custom Hoodie',
            'price' => '15000',
            'currency' => 'GHS',
            'unlimited' => true
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('PROD_xyz', $response['data']['product_code']);

        Http::assertSent(function ($request) {
            return $request['price'] === 15000 &&
                $request->method() === 'POST' &&
                $request->url() === 'https://api.paystack.co/product';
        });
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_all_products()
    {
        Http::fake([
            'api.paystack.co/product*' => Http::response([
                'status' => true,
                'data' => [
                    ['id' => 1, 'name' => 'Product 1'],
                    ['id' => 2, 'name' => 'Product 2']
                ]
            ], 200)
        ]);

        $response = Paystack::product()->list(['perPage' => 2]);

        $this->assertCount(2, $response['data']);
        Http::assertSent(fn ($request) => str_contains($request->url(), 'perPage=2'));
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_a_product_by_id()
    {
        $productId = 12345;
        Http::fake([
            "api.paystack.co/product/$productId" => Http::response([
                'status' => true,
                'data' => ['id' => $productId, 'name' => 'Custom Hoodie']
            ], 200)
        ]);

        $response = Paystack::product()->fetch($productId);

        $this->assertTrue($response['status']);
        $this->assertEquals($productId, $response['data']['id']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_update_a_product()
    {
        $productId = 12345;
        Http::fake([
            "api.paystack.co/product/$productId" => Http::response([
                'status' => true,
                'message' => 'Product updated'
            ], 200)
        ]);

        $response = Paystack::product()->update($productId, [
            'name' => 'Updated Hoodie Name',
            'price' => 20000
        ]);

        $this->assertTrue($response['status']);
        Http::assertSent(function ($request) use ($productId) {
            return $request->method() === 'PUT' &&
                $request->url() === "https://api.paystack.co/product/$productId" &&
                $request['price'] === 20000;
        });
    }
}