<?php

namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class PaymentRequestResource extends BaseResource
{
    /**
     * Create a payment request (Invoice) on your integration.
     * @param array $payload ['customer', 'amount', 'due_date', 'description', 'line_items', 'tax', 'currency', 'send_notification', 'draft', 'has_invoice', 'invoice_number', 'split_code']
     * @return array The created payment request details
     * @throws PaystackException|ConnectionException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/paymentrequest", $payload);

        return $this->handleResponse($response);
    }

    /**
     * List the payment requests available on your integration.
     * @param array $filters ['perPage', 'page', 'customer', 'status', 'currency', 'include_archive', 'from', 'to']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function list(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/paymentrequest", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get details of a payment request on your integration.
     * @param string|int $idOrCode The payment request ID or code
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function fetch(string|int $idOrCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/paymentrequest/$idOrCode");

        return $this->handleResponse($response);
    }

    /**
     * Verify details of a payment request.
     * @param string $code Payment Request code
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function verify(string $code): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/paymentrequest/verify/$code");

        return $this->handleResponse($response);
    }

    /**
     * Send notification of a payment request to your customers.
     * @param string $code Payment Request code
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function notify(string $code): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/paymentrequest/notify/$code");

        return $this->handleResponse($response);
    }

    /**
     * Get payment requests metrics (totals for pending, successful, etc).
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function totals(): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/paymentrequest/totals");

        return $this->handleResponse($response);
    }

    /**
     * Finalize a draft payment request.
     * @param string $code Payment Request code
     * @param bool $sendNotification Indicates whether to send email notification
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function finalize(string $code, bool $sendNotification = true): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/paymentrequest/finalize/$code", [
            'send_notification' => $sendNotification
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Update a payment request details.
     * @param string|int $idOrCode Payment Request ID or code
     * @param array $payload ['customer', 'amount', 'due_date', 'description', 'line_items', 'tax', 'currency', 'send_notification', 'draft', 'invoice_number', 'split_code']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function update(string|int $idOrCode, array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->put("$this->baseUrl/paymentrequest/$idOrCode", $payload);

        return $this->handleResponse($response);
    }

    /**
     * Archive a payment request.
     * @param string $code Payment Request code
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function archive(string $code): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/paymentrequest/archive/$code");

        return $this->handleResponse($response);
    }
}