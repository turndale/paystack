<?php

namespace StephenAsare\Paystack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use StephenAsare\Paystack\Events\WebhookReceived;
use StephenAsare\Paystack\Events\WebhookHandled;
use StephenAsare\Paystack\Events\PaymentSuccess;
use StephenAsare\Paystack\Events\SubscriptionCreated;
use StephenAsare\Paystack\Events\SubscriptionUpdated;
use StephenAsare\Paystack\Events\InvoiceCreated;
use StephenAsare\Paystack\Events\InvoiceUpdated;
use StephenAsare\Paystack\Events\InvoicePaymentFailed;
use StephenAsare\Paystack\Events\ChargeDisputeCreated;
use StephenAsare\Paystack\Events\TransferSuccess;
use StephenAsare\Paystack\Events\TransferFailed;

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

        // 1. Immediately return 200 OK to Paystack to prevent timeouts and retries
        // Use fast_finish if your server supports it, or simply return the response object.
        $response = new Response('Webhook Received', 200);

        // 2. Dispatch events after the response is prepared.
        // Developers should use Queued Listeners to ensure this doesn't delay the response.
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
        $this->validateAndDispatch('charge.success', PaymentSuccess::class, $payload);
    }

    /**
     * Handle a subscription creation.
     */
    protected function handleSubscriptionCreate(array $payload)
    {
        $this->validateAndDispatch('subscription.create', SubscriptionCreated::class, $payload);
    }

    /**
     * Handle a subscription disable event.
     */
    protected function handleSubscriptionDisable(array $payload)
    {
        $this->validateAndDispatch('subscription.disable', SubscriptionUpdated::class, $payload);
    }

    /**
     * Handle a subscription not renewing event.
     */
    protected function handleSubscriptionNotRenew(array $payload)
    {
        $this->validateAndDispatch('subscription.not_renew', SubscriptionUpdated::class, $payload);
    }

    /**
     * Handle an invoice creation event.
     */
    protected function handleInvoiceCreate(array $payload)
    {
        $this->validateAndDispatch('invoice.create', InvoiceCreated::class, $payload);
    }

    /**
     * Handle an invoice update event.
     */
    protected function handleInvoiceUpdate(array $payload)
    {
        $this->validateAndDispatch('invoice.update', InvoiceUpdated::class, $payload);
    }

    /**
     * Handle an invoice payment failure event.
     */
    protected function handleInvoicePaymentFailed(array $payload)
    {
        $this->validateAndDispatch('invoice.payment_failed', InvoicePaymentFailed::class, $payload);
    }

    /**
     * Handle a charge dispute creation event.
     */
    protected function handleChargeDisputeCreate(array $payload)
    {
        $this->validateAndDispatch('charge.dispute.create', ChargeDisputeCreated::class, $payload);
    }

    /**
     * Handle a transfer success event.
     */
    protected function handleTransferSuccess(array $payload)
    {
        $this->validateAndDispatch('transfer.success', TransferSuccess::class, $payload);
    }

    /**
     * Handle a transfer failure event.
     */
    protected function handleTransferFailed(array $payload)
    {
        $this->validateAndDispatch('transfer.failed', TransferFailed::class, $payload);
    }
}