<?php

namespace HelloFromSteve\Paystack\Tests\Feature;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use HelloFromSteve\Paystack\Facades\Paystack;
use HelloFromSteve\Paystack\Tests\PaystackTestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class DisputeResourceTest extends PaystackTestCase
{
    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_list_disputes()
    {
        Http::fake([
            'api.paystack.co/dispute*' => Http::response([
                'status' => true,
                'message' => 'Disputes retrieved',
                'data' => [['id' => 123, 'status' => 'awaiting-merchant-feedback']]
            ], 200)
        ]);

        $response = Paystack::dispute()->list(['status' => 'active']);

        $this->assertTrue($response['status']);
        $this->assertCount(1, $response['data']);
        Http::assertSent(fn ($request) => $request->url() === 'https://api.paystack.co/dispute?status=active');
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_fetch_a_single_dispute()
    {
        $disputeId = '889900';
        Http::fake([
            "api.paystack.co/dispute/$disputeId" => Http::response([
                'status' => true,
                'data' => ['id' => $disputeId]
            ], 200)
        ]);

        $response = Paystack::dispute()->fetch($disputeId);

        $this->assertTrue($response['status']);
        $this->assertEquals($disputeId, $response['data']['id']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_update_a_dispute()
    {
        $disputeId = '889900';
        Http::fake([
            "api.paystack.co/dispute/$disputeId" => Http::response([
                'status' => true,
                'message' => 'Dispute updated'
            ], 200)
        ]);

        $response = Paystack::dispute()->update($disputeId, [
            'refund_amount' => '5000'
        ]);

        $this->assertTrue($response['status']);
        Http::assertSent(fn ($request) => $request['refund_amount'] === 5000);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_add_evidence_to_a_dispute()
    {
        $disputeId = '889900';
        Http::fake([
            "api.paystack.co/dispute/$disputeId/evidence" => Http::response([
                'status' => true,
                'message' => 'Evidence submitted successfully'
            ], 200)
        ]);

        $response = Paystack::dispute()->addEvidence($disputeId, [
            'customer_email' => 'stephen@stephenasare.dev',
            'customer_name' => 'Stephen Asare',
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('Evidence submitted successfully', $response['message']);
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_get_a_signed_upload_url()
    {
        $disputeId = '889900';
        Http::fake([
            "api.paystack.co/dispute/$disputeId/upload_url*" => Http::response([
                'status' => true,
                'data' => ['signed_url' => 'https://s3.signed.url/evidence.pdf']
            ], 200)
        ]);

        $response = Paystack::dispute()->getUploadUrl($disputeId, 'evidence.pdf');

        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('signed_url', $response['data']);
        Http::assertSent(fn ($request) => $request->url() === "https://api.paystack.co/dispute/$disputeId/upload_url?upload_filename=evidence.pdf");
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_resolve_a_dispute()
    {
        $disputeId = '889900';
        Http::fake([
            "api.paystack.co/dispute/$disputeId/resolve" => Http::response([
                'status' => true,
                'message' => 'Dispute resolved'
            ], 200)
        ]);

        $response = Paystack::dispute()->resolve($disputeId, [
            'resolution' => 'merchant-accepted',
            'message' => 'Resolved'
        ]);

        $this->assertTrue($response['status']);
        $this->assertEquals('Dispute resolved', $response['message']);
        Http::assertSent(fn ($request) => $request->url() === "https://api.paystack.co/dispute/$disputeId/resolve");
    }

    /**
     * @throws PaystackException
     * @throws ConnectionException
     */
    #[Test]
    public function it_can_export_disputes()
    {
        Http::fake([
            'api.paystack.co/dispute/export*' => Http::response([
                'status' => true,
                'data' => ['export_url' => 'https://paystack.com/export/123.csv']
            ], 200)
        ]);

        $response = Paystack::dispute()->export(['status' => 'resolved']);

        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('export_url', $response['data']);
    }
}