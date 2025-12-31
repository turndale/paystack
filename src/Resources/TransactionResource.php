<?php


namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class TransactionResource extends BaseResource
{
    /**
     * Initialize a transaction to generate a checkout link.
     * * @param array $payload ['email', 'amount', 'callback_url', 'plan', 'metadata', 'channels']
     * @return array The Paystack API response containing authorization_url and access_code
     * @throws PaystackException|ConnectionException
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

        return $this->handleResponse($this->request()->post("$this->baseUrl/transaction/initialize", $payload));
    }

    /**
     * Helper to get only the redirect URL for a transaction.
     * * @param array $payload
     * @return string Redirect URL to Paystack Checkout
     * @throws PaystackException|ConnectionException
     */
    public function getAuthorizationUrl(array $payload): string
    {
        $response = $this->initialize($payload);
        return $response['data']['authorization_url'];
    }

    /**
     * Confirm the status of a transaction.
     * * @param string $reference Unique case-sensitive transaction reference
     * @return array Transaction details and status
     * @throws PaystackException|ConnectionException
     */
    public function verify(string $reference): array
    {
        return $this->handleResponse($this->request()->get("$this->baseUrl/transaction/verify/$reference"));
    }

    /**
     * List transactions carried out on your integration.
     * * @param array $filters ['perPage', 'page', 'customer', 'status', 'from', 'to']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        return $this->handleResponse($this->request()->get("$this->baseUrl/transaction", $filters));
    }

    /**
     * Get details of a single transaction.
     * * @param string|int $id Transaction ID
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string|int $id): array
    {
        return $this->handleResponse($this->request()->get("$this->baseUrl/transaction/$id"));
    }
}