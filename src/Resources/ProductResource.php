<?php

namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class ProductResource extends BaseResource
{
    /**
     * Create a product on your integration.
     * @param array $payload ['name', 'description', 'price', 'currency', 'unlimited', 'quantity']
     * @return array The created product details
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        if (isset($payload['price'])) {
            $payload['price'] = (int) $payload['price'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/product", $payload);

        return $this->handleResponse($response);
    }

    /**
     * List products available on your integration.
     * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/product", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get details of a product on your integration.
     * @param string|int $id The product ID
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string|int $id): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/product/$id");

        return $this->handleResponse($response);
    }

    /**
     * Update a product's details.
     * @param string|int $id Product ID
     * @param array $payload ['name', 'description', 'price', 'currency', 'unlimited', 'quantity']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function update(string|int $id, array $payload): array
    {
        if (isset($payload['price'])) {
            $payload['price'] = (int) $payload['price'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->put("$this->baseUrl/product/$id", $payload);

        return $this->handleResponse($response);
    }
}