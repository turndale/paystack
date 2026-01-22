<?php

namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class TransactionSplitResource extends BaseResource
{
    /**
     * Create a split payment on your integration.
     * @param array $payload ['name', 'type', 'currency', 'subaccounts', 'bearer_type', 'bearer_subaccount']
     * @return array
     * @throws PaystackException
     */
    public function create(array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/split", $payload);

        return $this->handleResponse($response);
    }

    /**
     * List the transaction splits available on your integration.
     * @param array $filters ['name', 'active', 'sort_by', 'perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/split", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get details of a split on your integration.
     * @param string $id The id of the split
     * @return array
     * @throws PaystackException
     */
    public function fetch(string $id): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/split/$id");

        return $this->handleResponse($response);
    }

    /**
     * Update a transaction split details on your integration.
     * @param string $id Split ID
     * @param array $payload ['name', 'active', 'bearer_type', 'bearer_subaccount']
     * @return array
     * @throws PaystackException
     */
    public function update(string $id, array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->put("$this->baseUrl/split/$id", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Add a Subaccount to a Transaction Split, or update the share of an existing Subaccount in a Transaction Split.
     * @param string $id Split Id
     * @param array $payload ['subaccount', 'share']
     * @return array
     * @throws PaystackException
     */
    public function addSubaccount(string $id, array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/split/$id/subaccount/add", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Remove a subaccount from a transaction split.
     * @param string $id Split Id
     * @param string $subaccountCode This is the sub account code
     * @return array
     * @throws PaystackException
     */
    public function removeSubaccount(string $id, string $subaccountCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/split/$id/subaccount/remove", [
            'subaccount' => $subaccountCode
        ]);

        return $this->handleResponse($response);
    }
}
