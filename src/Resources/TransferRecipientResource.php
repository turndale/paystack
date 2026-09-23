<?php

namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class TransferRecipientResource extends BaseResource
{
    /**
     * Creates a new recipient. A duplicate account number resolves to the existing record instead of erroring.
     * @param array $payload ['type', 'name', 'account_number', 'bank_code', 'currency', 'description',
     *   'authorization_code', 'metadata']
     *   'type' is one of: nuban, ghipss, mobile_money, basa.
     *   'account_number'/'bank_code' are required for every type except an authorization-based recipient
     *   (set 'authorization_code' instead; reuses a customer's existing authorization).
     * @return array The created recipient details including recipient_code
     * @throws PaystackException
     */
    public function create(array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transferrecipient", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Create multiple transfer recipients in batches. A duplicate account number in the batch resolves to the
     * existing record rather than erroring.
     * @param array $batch Array of recipient objects
     * @return array data.success holds the accepted recipients; data.errors holds any rejected ones.
     * @throws PaystackException
     */
    public function bulkCreate(array $batch): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transferrecipient/bulk", [
            'batch' => $batch
        ]);

        return $this->handleResponse($response);
    }

    /**
     * List transfer recipients available on your integration.
     * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/transferrecipient", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Fetch the details of a specific transfer recipient.
     * @param string|int $idOrCode
     * @return array data.details holds account_number, account_name (frequently null), bank_code, bank_name,
     *   and (for authorization-based recipients) authorization_code.
     * @throws PaystackException
     */
    public function fetch(string|int $idOrCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/transferrecipient/$idOrCode");

        return $this->handleResponse($response);
    }

    /**
     * Update a transfer recipient's details.
     * @param string|int $idOrCode
     * @param array $payload ['name', 'email']
     * @return array The full updated recipient, not just a message.
     * @throws PaystackException
     */
    public function update(string|int $idOrCode, array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->put("$this->baseUrl/transferrecipient/$idOrCode", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Delete a transfer recipient (sets the recipient to inactive).
     * @param string|int $idOrCode
     * @return array Only status/message; there is no data key at all on a successful delete.
     * @throws PaystackException
     */
    public function delete(string|int $idOrCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->delete("$this->baseUrl/transferrecipient/$idOrCode");

        return $this->handleResponse($response);
    }
}