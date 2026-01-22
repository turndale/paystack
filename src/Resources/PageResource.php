<?php


namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class PageResource extends BaseResource
{
    /**
     * Create a payment page on your integration.
     * @param array $payload ['name', 'description', 'amount', 'slug', 'redirect_url', etc.]
     * @return array Created page details
     * @throws PaystackException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/page", $payload);

        return $this->handleResponse($response);
    }

    /**
     * List payment pages available on your integration.
     * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array
     * @throws PaystackException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/page", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get details of a payment page.
     * @param string $idOrSlug The page ID or URL slug
     * @return array
     * @throws PaystackException
     */
    public function fetch(string $idOrSlug): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/page/$idOrSlug");

        return $this->handleResponse($response);
    }

    /**
     * Update a payment page details.
     * @param string $idOrSlug
     * @param array $payload ['name', 'description', 'amount', 'active']
     * @return array
     * @throws PaystackException
     */
    public function update(string $idOrSlug, array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->put("$this->baseUrl/page/$idOrSlug", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Check the availability of a slug for a payment page.
     * @param string $slug
     * @return array
     * @throws PaystackException
     */
    public function checkSlugAvailability(string $slug): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/page/check_slug_availability/$slug");

        return $this->handleResponse($response);
    }

    /**
     * Add products to a payment page.
     * @param int $id ID of the payment page
     * @param array $products Array of product IDs [123, 456]
     * @return array
     * @throws PaystackException
     */
    public function addProducts(int $id, array $products): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/page/$id/product", [
            'product' => $products
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Redirect to the payment page.
     * @param string $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(string $slug): \Illuminate\Http\RedirectResponse
    {
        return redirect()->away("https://paystack.com/pay/$slug");
    }
}