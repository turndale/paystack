<?php


namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class PageResource extends BaseResource
{
    /**
     * Create a payment page on your integration.
     * * @param array $payload ['name', 'description', 'amount', 'slug', 'redirect_url', etc.]
     * @return array Created page details
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/page", $payload)
        );
    }

    /**
     * List payment pages available on your integration.
     * * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/page", $filters)
        );
    }

    /**
     * Get details of a payment page.
     * * @param string $idOrSlug The page ID or URL slug
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string $idOrSlug): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/page/$idOrSlug")
        );
    }

    /**
     * Update a payment page details.
     * * @param string $idOrSlug
     * @param array $payload ['name', 'description', 'amount', 'active']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function update(string $idOrSlug, array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        return $this->handleResponse(
            $this->request()->put("$this->baseUrl/page/$idOrSlug", $payload)
        );
    }

    /**
     * Check the availability of a slug for a payment page.
     * * @param string $slug
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function checkSlugAvailability(string $slug): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/page/check_slug_availability/$slug")
        );
    }

    /**
     * Add products to a payment page.
     * * @param int $id ID of the payment page
     * @param array $products Array of product IDs [123, 456]
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function addProducts(int $id, array $products): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/page/$id/product", [
                'product' => $products
            ])
        );
    }
}