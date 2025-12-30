<?php

namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class PlanResource extends BaseResource
{
    /**
     * Create a new subscription plan.
     * * @param array $payload ['name', 'amount', 'interval', 'description', 'currency']
     * @return array Created plan details including plan_code
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        return $this->handleResponse($this->request()->post("$this->baseUrl/plan", $payload));
    }

    /**
     * List all plans.
     * * @param array $filters ['perPage', 'page', 'interval', 'amount']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        return $this->handleResponse($this->request()->get("$this->baseUrl/plan", $filters));
    }

    /**
     * Get details of a plan.
     * * @param string $idOrCode Plan ID or Code
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string $idOrCode): array
    {
        return $this->handleResponse($this->request()->get("$this->baseUrl/plan/$idOrCode"));
    }

    /**
     * Update a plan.
     * * @param string $idOrCode
     * @param array $payload ['name', 'amount', 'description', 'update_existing_subscriptions']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function update(string $idOrCode, array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        return $this->handleResponse($this->request()->put("$this->baseUrl/plan/$idOrCode", $payload));
    }
}