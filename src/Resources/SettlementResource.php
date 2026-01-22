<?php

namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class SettlementResource extends BaseResource
{
    /**
     * List settlements made to your settlement accounts.
     * @param array $filters ['perPage', 'page', 'status', 'subaccount', 'from', 'to']
     * @return array
     * @throws PaystackException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/settlement", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get the transactions that make up a particular settlement.
     * @param string|int $id The settlement ID
     * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException
     */
    public function transactions(string|int $id, array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/settlement/$id/transactions", $filters);

        return $this->handleResponse($response);
    }
}