<?php

namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class SubaccountResource extends BaseResource
{
    /**
     * Create a subaccount on your integration.
     * @param array $payload ['business_name', 'settlement_bank', 'account_number', 'percentage_charge', 'description', 'primary_contact_email', 'primary_contact_name', 'primary_contact_phone', 'metadata']
     * @return array
     * @throws PaystackException
     */
    public function create(array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/subaccount", $payload);

        return $this->handleResponse($response);
    }

    /**
     * List subaccounts available on your integration.
     * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/subaccount", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get details of a subaccount on your integration.
     * @param string $idOrCode The subaccount ID or code
     * @return array
     * @throws PaystackException
     */
    public function fetch(string $idOrCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/subaccount/$idOrCode");

        return $this->handleResponse($response);
    }

    /**
     * Update a subaccount details on your integration.
     * @param string $idOrCode Subaccount ID or code
     * @param array $payload ['business_name', 'settlement_bank', 'account_number', 'percentage_charge', 'description', 'primary_contact_email', 'primary_contact_name', 'primary_contact_phone', 'metadata', 'active']
     * @return array
     * @throws PaystackException
     */
    public function update(string $idOrCode, array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->put("$this->baseUrl/subaccount/$idOrCode", $payload);

        return $this->handleResponse($response);
    }
}
