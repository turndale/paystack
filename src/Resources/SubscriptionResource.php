<?php


namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;

class SubscriptionResource extends BaseResource
{
    /**
     * Create a subscription.
     * @param array $payload ['customer', 'plan', 'authorization', 'start_date']
     * @return array
     * @throws PaystackException
     */
    public function create(array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/subscription", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Disable a subscription (Cancel).
     * @param string $code Subscription code
     * @param string $token Email token
     * @return array
     * @throws PaystackException
     */
    public function disable(string $code, string $token): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/subscription/disable", [
            'code' => $code,
            'token' => $token
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Generate a link for the customer to update their card.
     * @param string $code Subscription code
     * @return string The secure management link
     * @throws PaystackException
     */
    public function getUpdateLink(string $code): string
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/subscription/$code/manage/link");

        $data = $this->handleResponse($response);
        return $data['data']['link'];
    }
}