<?php

namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class CustomerResource extends BaseResource
{
    /**
     * Create a customer on your integration.
     * * @param array $payload ['email', 'first_name', 'last_name', 'phone', 'metadata']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        return $this->handleResponse($this->request()->post("$this->baseUrl/customer", $payload));
    }

    /**
     * Get a customer's details.
     * * @param string $emailOrCode
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string $emailOrCode): array
    {
        return $this->handleResponse($this->request()->get("$this->baseUrl/customer/$emailOrCode"));
    }


    /**
     * Update a customer's details on your integration.
     * * @param string $code Customer's code
     * @param array $payload ['first_name', 'last_name', 'phone', 'metadata']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function update(string $code, array $payload): array
    {
        return $this->handleResponse($this->request()->put("$this->baseUrl/customer/$code", $payload));
    }

    /**
     * Validate a customer's identity.
     * * @param string $code Email, or customer code of customer to be identified
     * @param array $payload ['first_name', 'last_name', 'type', 'value', 'country', 'bvn', 'bank_code', 'account_number']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function validate(string $code, array $payload): array
    {
        return $this->handleResponse($this->request()->post("$this->baseUrl/customer/$code/identification", $payload));
    }

    /**
     * Whitelist or blacklist a customer on your integration.
     * * @param array $payload ['customer', 'risk_action']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function setRiskAction(array $payload): array
    {
        return $this->handleResponse($this->request()->post("$this->baseUrl/customer/set_risk_action", $payload));
    }

    /**
     * Deactivate an authorization for any payment channel.
     * * @param string $authorizationCode Authorization code to be deactivated
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function deactivateAuthorization(string $authorizationCode): array
    {
        return $this->handleResponse($this->request()->post("$this->baseUrl/customer/authorization/deactivate", [
            'authorization_code' => $authorizationCode
        ]));
    }

    /**
     * Initiate a request to create a reusable authorization code for recurring transactions.
     * * @param array $payload ['email', 'channel', 'callback_url', 'account', 'address']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function initializeAuthorization(array $payload): array
    {
        return $this->handleResponse($this->request()->post("$this->baseUrl/customer/authorization/initialize", $payload));
    }

    /**
     * Check the status of an authorization request.
     * * @param string $reference The reference returned in the initialization response
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function verifyAuthorization(string $reference): array
    {
        return $this->handleResponse($this->request()->get("$this->baseUrl/customer/authorization/verify/$reference"));
    }
}