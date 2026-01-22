<?php

namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class TransferResource extends BaseResource
{
    /**
     * Send money to a customer/recipient.
     * @param array $payload ['source', 'amount', 'recipient', 'reference', 'reason', 'currency']
     * @return array Transfer details. Status will be 'pending', 'success', or 'otp'.
     * @throws PaystackException
     */
    public function initiate(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        // Source is currently limited to 'balance' by Paystack
        $payload['source'] = $payload['source'] ?? 'balance';

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transfer", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Finalize an initiated transfer (Required if OTP is enabled).
     * @param string $transferCode The code from the initiate response
     * @param string $otp The OTP sent to the business phone
     * @return array
     * @throws PaystackException
     */
    public function finalize(string $transferCode, string $otp): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transfer/finalize_transfer", [
            'transfer_code' => $transferCode,
            'otp' => $otp
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Batch multiple transfers in a single request.
     * Note: You must disable 'Transfers OTP' in Paystack Dashboard to use this.
     * @param array $transfers List of transfer objects [['amount', 'recipient', 'reference', 'reason']]
     * @param string $source Defaults to 'balance'
     * @param string $currency Defaults to 'NGN'
     * @return array
     * @throws PaystackException
     */
    public function bulk(array $transfers, string $source = 'balance', string $currency = 'NGN'): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transfer/bulk", [
            'source' => $source,
            'currency' => $currency,
            'transfers' => $transfers
        ]);

        return $this->handleResponse($response);
    }

    /**
     * List all transfers made on your integration.
     * @param array $filters ['perPage', 'page', 'recipient', 'from', 'to']
     * @return array
     * @throws PaystackException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/transfer", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get details of a single transfer.
     * @param string|int $idOrCode Transfer ID or Code
     * @return array
     * @throws PaystackException
     */
    public function fetch(string|int $idOrCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/transfer/$idOrCode");

        return $this->handleResponse($response);
    }

    /**
     * Verify the status of a transfer using its reference.
     * @param string $reference
     * @return array
     * @throws PaystackException
     */
    public function verify(string $reference): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/transfer/verify/$reference");

        return $this->handleResponse($response);
    }
}