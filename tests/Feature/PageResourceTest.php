<?php

namespace HelloFromSteve\Paystack\Tests\Feature;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use HelloFromSteve\Paystack\Facades\Paystack;
use HelloFromSteve\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class PageResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_create_a_payment_page()
    {
        Http::fake([
            'api.paystack.co/page' => Http::response([
                'status' => true,
                'message' => 'Page created',
                'data' => ['name' => 'Testing Page', 'slug' => 'test-page']
            ], 200)
        ]);

        $response = Paystack::page()->create([
            'name' => 'Testing Page',
            'amount' => '5000'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('test-page', $response['data']['slug']);

        Http::assertSent(fn ($request) =>
            $request['amount'] === 5000 && $request->method() === 'POST'
        );
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_payment_pages()
    {
        Http::fake([
            'api.paystack.co/page*' => Http::response([
                'status' => true,
                'data' => [['id' => 1], ['id' => 2]]
            ], 200)
        ]);

        $response = Paystack::page()->list(['perPage' => 2]);

        $this->assertCount(2, $response['data']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_a_page_by_id_or_slug()
    {
        $slug = 'test-page';
        Http::fake([
            "api.paystack.co/page/$slug" => Http::response([
                'status' => true,
                'data' => ['slug' => $slug]
            ], 200)
        ]);

        $response = Paystack::page()->fetch($slug);

        $this->assertEquals($slug, $response['data']['slug']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_update_a_payment_page()
    {
        $id = '12345';
        Http::fake([
            "api.paystack.co/page/$id" => Http::response([
                'status' => true,
                'message' => 'Page updated'
            ], 200)
        ]);

        $response = Paystack::page()->update($id, [
            'name' => 'New Name',
            'amount' => 10000
        ]);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request->method() === 'PUT');
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_check_slug_availability()
    {
        $slug = 'unique-slug';
        Http::fake([
            "api.paystack.co/page/check_slug_availability/$slug" => Http::response([
                'status' => true,
                'message' => 'Slug is available'
            ], 200)
        ]);

        $response = Paystack::page()->checkSlugAvailability($slug);

        $this->assertTrue($response['status']);
        $this->assertEquals('Slug is available', $response['message']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_add_products_to_a_payment_page()
    {
        $pageId = 123;
        Http::fake([
            "api.paystack.co/page/$pageId/product" => Http::response([
                'status' => true,
                'message' => 'Products added to page'
            ], 200)
        ]);

        $response = Paystack::page()->addProducts($pageId, [44, 55]);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) =>
            $request['product'] === [44, 55]
        );
    }
}