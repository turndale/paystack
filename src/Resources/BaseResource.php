<?php


namespace Turndale\Paystack\Resources;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\PendingRequest;
use Turndale\Paystack\Exceptions\PaystackException;

abstract class BaseResource
{
    public function __construct(
        protected string $secret,
        protected string $baseUrl
    ) {}

    protected function request(): PendingRequest
    {
        return Http::withToken($this->secret)
            ->acceptJson()
            ->timeout(45);
    }

    protected function handleResponse(Response $response): array
    {
        $data = $response->json();

        if ($response->failed() || (isset($data['status']) && $data['status'] === false)) {
            throw new PaystackException(
                message: $data['message'] ?? 'An unknown Paystack error occurred',
                httpCode: $response->status(),
                type: $data['type'] ?? 'api_error',
                paystackCode: $data['code'] ?? null,
                meta: $data['meta'] ?? []
            );
        }

        return $data;
    }
}