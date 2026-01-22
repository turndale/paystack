<?php


namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class TransactionResource extends BaseResource
{
    /**
     * Initialize a transaction to generate a checkout link.
     * @param array $payload ['email', 'amount', 'callback_url', 'plan', 'metadata', 'channels']
     * @return array The Paystack API response containing authorization_url and access_code
     * @throws PaystackException
     */
    public function initialize(array $payload): array
    {
        if (!isset($payload['callback_url'])) {
            $payload['callback_url'] = config('paystack.callback_url');
        }

        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        if (isset($payload['metadata']) && is_array($payload['metadata'])) {
            $payload['metadata'] = json_encode($payload['metadata']);
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transaction/initialize", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Helper to get only the redirect URL for a transaction.
     * @param array $payload
     * @return string Redirect URL to Paystack Checkout
     * @throws PaystackException
     */
    public function getAuthorizationUrl(array $payload): string
    {
        $response = $this->initialize($payload);
        return $response['data']['authorization_url'];
    }

    /**
     * Confirm the status of a transaction.
     * @param string $reference Unique case-sensitive transaction reference
     * @return array Transaction details and status
     * @throws PaystackException
     */
    public function verify(string $reference): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/transaction/verify/$reference");
        return $this->handleResponse($response);
    }

    /**
     * List transactions carried out on your integration.
     * @param array $filters ['perPage', 'page', 'customer', 'status', 'from', 'to']
     * @return array
     * @throws PaystackException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/transaction", $filters);
        return $this->handleResponse($response);
    }

    /**
     * Get details of a single transaction.
     * @param string|int $id Transaction ID
     * @return array
     * @throws PaystackException
     */
    public function fetch(string|int $id): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/transaction/$id");
        return $this->handleResponse($response);
    }
}