<?php


namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class SubscriptionResource extends BaseResource
{
    /**
     * Create a subscription.
     * * @param array $payload ['customer', 'plan', 'authorization', 'start_date']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        return $this->handleResponse($this->request()->post("$this->baseUrl/subscription", $payload));
    }

    /**
     * Disable a subscription (Cancel).
     * * @param string $code Subscription code
     * @param string $token Email token
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function disable(string $code, string $token): array
    {
        return $this->handleResponse($this->request()->post("$this->baseUrl/subscription/disable", [
            'code' => $code,
            'token' => $token
        ]));
    }

    /**
     * Generate a link for the customer to update their card.
     * * @param string $code Subscription code
     * @return string The secure management link
     * @throws PaystackException|ConnectionException
     */
    public function getUpdateLink(string $code): string
    {
        $response = $this->handleResponse($this->request()->get("$this->baseUrl/subscription/$code/manage/link"));
        return $response['data']['link'];
    }
}