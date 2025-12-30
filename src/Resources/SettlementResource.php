<?php

namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class SettlementResource extends BaseResource
{
    /**
     * List settlements made to your settlement accounts.
     * * @param array $filters ['perPage', 'page', 'status', 'subaccount', 'from', 'to']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/settlement", $filters)
        );
    }

    /**
     * Get the transactions that make up a particular settlement.
     * * @param string|int $id The settlement ID
     * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function transactions(string|int $id, array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/settlement/$id/transactions", $filters)
        );
    }
}