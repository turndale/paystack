<?php

namespace Turndale\Paystack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Turndale\Paystack\Events\WebhookReceived;
use Turndale\Paystack\Events\WebhookHandled;

class WebhookController extends Controller
{
    /**
     * Handle a Paystack webhook call.
     *
     * @param Request $request
     * @return Response
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        $event = $payload['event'] ?? null;

        $response = new Response('Webhook Received', 200);

        // General event
        WebhookReceived::dispatch($payload);

        if ($event) {
            $method = 'handle' . Str::studly(str_replace('.', '_', $event));

            if (method_exists($this, $method)) {
                $this->{$method}($payload);
                WebhookHandled::dispatch($payload);
            }
        }

        return $response;
    }

    /**
     * Helper to verify event type before dispatching.
     */
    protected function validateAndDispatch(string $expected, string $eventClass, array $payload): void
    {
        if (($payload['event'] ?? null) === $expected) {
            $eventClass::dispatch($payload);
        }
    }

    /**
     * Handle a successful charge.
     */
    protected function handleChargeSuccess(array $payload)
    {
        $this->validateAndDispatch('charge.success', \Turndale\Paystack\Events\PaymentSuccess::class, $payload);
    }

    /**
     * Handle a subscription creation.
     */
    protected function handleSubscriptionCreate(array $payload)
    {
        $this->validateAndDispatch('subscription.create', \Turndale\Paystack\Events\SubscriptionCreated::class, $payload);
    }

    /**
     * Handle a subscription disable event.
     */
    protected function handleSubscriptionDisable(array $payload)
    {
        $this->validateAndDispatch('subscription.disable', \Turndale\Paystack\Events\SubscriptionDisabled::class, $payload);
    }

    /**
     * Handle a subscription not renewing event.
     */
    protected function handleSubscriptionNotRenew(array $payload)
    {
        $this->validateAndDispatch('subscription.not_renew', \Turndale\Paystack\Events\SubscriptionNotRenew::class, $payload);
    }

    /**
     * Handle an invoice creation event.
     */
    protected function handleInvoiceCreate(array $payload)
    {
        $this->validateAndDispatch('invoice.create', \Turndale\Paystack\Events\InvoiceCreated::class, $payload);
    }

    /**
     * Handle an invoice update event.
     */
    protected function handleInvoiceUpdate(array $payload)
    {
        $this->validateAndDispatch('invoice.update', \Turndale\Paystack\Events\InvoiceUpdated::class, $payload);
    }

    /**
     * Handle an invoice payment failure event.
     */
    protected function handleInvoicePaymentFailed(array $payload)
    {
        $this->validateAndDispatch('invoice.payment_failed', \Turndale\Paystack\Events\InvoicePaymentFailed::class, $payload);
    }

    /**
     * Handle a charge dispute creation event.
     */
    protected function handleChargeDisputeCreate(array $payload)
    {
        $this->validateAndDispatch('charge.dispute.create', \Turndale\Paystack\Events\ChargeDisputeCreated::class, $payload);
    }

    /**
     * Handle a transfer success event.
     */
    protected function handleTransferSuccess(array $payload)
    {
        $this->validateAndDispatch('transfer.success', \Turndale\Paystack\Events\TransferSuccess::class, $payload);
    }

    /**
     * Handle a transfer failure event.
     */
    protected function handleTransferFailed(array $payload)
    {
        $this->validateAndDispatch('transfer.failed', \Turndale\Paystack\Events\TransferFailed::class, $payload);
    }
}