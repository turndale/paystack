<?php


namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class DisputeResource extends BaseResource
{
    /**
     * List disputes filed against your integration.
     * * @param array $filters ['from', 'to', 'perPage', 'page', 'transaction', 'status']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/dispute", $filters)
        );
    }

    /**
     * Get more details about a specific dispute.
     * * @param string|int $id The dispute ID
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string|int $id): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/dispute/$id")
        );
    }

    /**
     * Retrieve disputes for a particular transaction.
     * * @param string|int $transactionId
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function listByTransaction(string|int $transactionId): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/dispute/transaction/$transactionId")
        );
    }

    /**
     * Update details of a dispute, such as the refund amount.
     * * @param string|int $id Dispute ID
     * @param array $payload ['refund_amount', 'uploaded_filename']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function update(string|int $id, array $payload): array
    {
        if (isset($payload['refund_amount'])) {
            $payload['refund_amount'] = (int) $payload['refund_amount'];
        }

        return $this->handleResponse(
            $this->request()->put("$this->baseUrl/dispute/$id", $payload)
        );
    }

    /**
     * Provide evidence for a dispute to prove the service was rendered.
     * * @param string|int $id Dispute ID
     * @param array $payload ['customer_email', 'customer_name', 'customer_phone', 'service_details', etc.]
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function addEvidence(string|int $id, array $payload): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/dispute/$id/evidence", $payload)
        );
    }

    /**
     * Get a signed URL to upload a file (PDF/Image) as evidence for a dispute.
     * * @param string|int $id Dispute ID
     * @param string $filename The name of the file with extension (e.g. evidence.pdf)
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function getUploadUrl(string|int $id, string $filename): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/dispute/$id/upload_url", [
                'upload_filename' => $filename
            ])
        );
    }

    /**
     * Resolve a dispute on your integration.
     * * @param string|int $id Dispute ID
     * @param array $payload ['resolution', 'message', 'refund_amount', 'uploaded_filename', 'evidence']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function resolve(string|int $id, array $payload): array
    {
        if (isset($payload['refund_amount'])) {
            $payload['refund_amount'] = (int) $payload['refund_amount'];
        }

        return $this->handleResponse(
            $this->request()->put("$this->baseUrl/dispute/$id/resolve", $payload)
        );
    }

    /**
     * Export disputes available on your integration as a report.
     * * @param array $filters ['from', 'to', 'perPage', 'page', 'transaction', 'status']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function export(array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/dispute/export", $filters)
        );
    }
}