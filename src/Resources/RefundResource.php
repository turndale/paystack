<?php

namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class RefundResource extends BaseResource
{
    /**
     * Initiate a refund on your integration.
     * * @param array $payload ['transaction', 'amount', 'currency', 'customer_note', 'merchant_note']
     * @return array The refund details and status
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/refund", $payload)
        );
    }

    /**
     * Retry a refund with a 'needs-attention' status.
     * Use this by providing the bank account details of the customer.
     * * @param int $id The ID of the previously initiated refund
     * @param array $accountDetails ['currency', 'account_number', 'bank_id']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function retry(int $id, array $accountDetails): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/refund/retry_with_customer_details/$id", [
                'refund_account_details' => $accountDetails
            ])
        );
    }

    /**
     * List refunds available on your integration.
     * * @param array $filters ['transaction', 'currency', 'from', 'to', 'perPage', 'page']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/refund", $filters)
        );
    }

    /**
     * Get details of a specific refund.
     * * @param int $id The ID of the refund
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(int $id): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/refund/$id")
        );
    }
}