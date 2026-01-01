<?php

namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class PlanResource extends BaseResource
{
    /**
     * Create a new subscription plan.
     * @param array $payload ['name', 'amount', 'interval', 'description', 'currency']
     * @return array Created plan details including plan_code
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/plan", $payload);

        return $this->handleResponse($response);
    }

    /**
     * List all plans.
     * @param array $filters ['perPage', 'page', 'interval', 'amount']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/plan", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get details of a plan.
     * @param string $idOrCode Plan ID or Code
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string $idOrCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/plan/$idOrCode");

        return $this->handleResponse($response);
    }

    /**
     * Update a plan.
     * @param string $idOrCode
     * @param array $payload ['name', 'amount', 'description', 'update_existing_subscriptions']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function update(string $idOrCode, array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->put("$this->baseUrl/plan/$idOrCode", $payload);

        return $this->handleResponse($response);
    }
}