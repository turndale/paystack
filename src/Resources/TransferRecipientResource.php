<?php

namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class TransferRecipientResource extends BaseResource
{
    /**
     * Creates a new recipient. 
     * * @param array $payload ['type', 'name', 'account_number', 'bank_code', 'currency', 'description', 'metadata']
     * @return array The created recipient details including recipient_code
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/transferrecipient", $payload)
        );
    }

    /**
     * Create multiple transfer recipients in batches.
     * * @param array $batch Array of recipient objects
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function bulkCreate(array $batch): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/transferrecipient/bulk", [
                'batch' => $batch
            ])
        );
    }

    /**
     * List transfer recipients available on your integration.
     * * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/transferrecipient", $filters)
        );
    }

    /**
     * Fetch the details of a specific transfer recipient.
     * * @param string|int $idOrCode
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string|int $idOrCode): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/transferrecipient/$idOrCode")
        );
    }

    /**
     * Update a transfer recipient's details.
     * * @param string|int $idOrCode
     * @param array $payload ['name', 'email']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function update(string|int $idOrCode, array $payload): array
    {
        return $this->handleResponse(
            $this->request()->put("$this->baseUrl/transferrecipient/$idOrCode", $payload)
        );
    }

    /**
     * Delete a transfer recipient (sets the recipient to inactive).
     * * @param string|int $idOrCode
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function delete(string|int $idOrCode): array
    {
        return $this->handleResponse(
            $this->request()->delete("$this->baseUrl/transferrecipient/$idOrCode")
        );
    }
}