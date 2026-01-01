<?php

namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;

class CustomerResource extends BaseResource
{
    /**
     * Create a customer on your integration.
     * @param array $payload ['email', 'first_name', 'last_name', 'phone', 'metadata']
     * @return array
     * @throws PaystackException
     */
    public function create(array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/customer", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Get a customer's details.
     * @param string $emailOrCode
     * @return array
     * @throws PaystackException
     */
    public function fetch(string $emailOrCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/customer/$emailOrCode");
        return $this->handleResponse($response);
    }


    /**
     * Update a customer's details on your integration.
     * @param string $code Customer's code
     * @param array $payload ['first_name', 'last_name', 'phone', 'metadata']
     * @return array
     * @throws PaystackException
     */
    public function update(string $code, array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->put("$this->baseUrl/customer/$code", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Validate a customer's identity.
     * @param string $code Email, or customer code of customer to be identified
     * @param array $payload ['first_name', 'last_name', 'type', 'value', 'country', 'bvn', 'bank_code', 'account_number']
     * @return array
     * @throws PaystackException
     */
    public function validate(string $code, array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/customer/$code/identification", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Whitelist or blacklist a customer on your integration.
     * @param array $payload ['customer', 'risk_action']
     * @return array
     * @throws PaystackException
     */
    public function setRiskAction(array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/customer/set_risk_action", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Deactivate an authorization for any payment channel.
     * @param string $authorizationCode Authorization code to be deactivated
     * @return array
     * @throws PaystackException
     */
    public function deactivateAuthorization(string $authorizationCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/customer/authorization/deactivate", [
            'authorization_code' => $authorizationCode
        ]);
        return $this->handleResponse($response);
    }

    /**
     * Initiate a request to create a reusable authorization code for recurring transactions.
     * @param array $payload ['email', 'channel', 'callback_url', 'account', 'address']
     * @return array
     * @throws PaystackException
     */
    public function initializeAuthorization(array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/customer/authorization/initialize", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Check the status of an authorization request.
     * @param string $reference The reference returned in the initialization response
     * @return array
     * @throws PaystackException
     */
    public function verifyAuthorization(string $reference): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/customer/authorization/verify/$reference");
        return $this->handleResponse($response);
    }
}