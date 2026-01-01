<?php

namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;

class RefundResource extends BaseResource
{
    /**
     * Initiate a refund on your integration.
     * @param array $payload ['transaction', 'amount', 'currency', 'customer_note', 'merchant_note']
     * @return array The refund details and status
     * @throws PaystackException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/refund", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Retry a refund with a 'needs-attention' status.
     * Use this by providing the bank account details of the customer.
     * @param int $id The ID of the previously initiated refund
     * @param array $accountDetails ['currency', 'account_number', 'bank_id']
     * @return array
     * @throws PaystackException
     */
    public function retry(int $id, array $accountDetails): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/refund/retry_with_customer_details/$id", [
            'refund_account_details' => $accountDetails
        ]);

        return $this->handleResponse($response);
    }

    /**
     * List refunds available on your integration.
     * @param array $filters ['transaction', 'currency', 'from', 'to', 'perPage', 'page']
     * @return array
     * @throws PaystackException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/refund", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get details of a specific refund.
     * @param int $id The ID of the refund
     * @return array
     * @throws PaystackException
     */
    public function fetch(int $id): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/refund/$id");

        return $this->handleResponse($response);
    }
}