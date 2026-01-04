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

        WebhookReceived::dispatch($payload);

        if ($event) {
            $method = 'handle' . Str::studly(str_replace('.', '_', $event));

            if (method_exists($this, $method)) {
                $this->{$method}($payload);
                
                WebhookHandled::dispatch($payload);
                
                return new Response('Webhook Handled', 200);
            }
        }

        return new Response('Webhook Received', 200);
    }

    /**
     * Handle a successful charge.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleChargeSuccess(array $payload)
    {
        PaymentSuccess::dispatch($payload);
    }

    /**
     * Handle a subscription creation.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleSubscriptionCreate(array $payload)
    {
        SubscriptionCreated::dispatch($payload);
    }

    /**
     * Handle a subscription disable event.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleSubscriptionDisable(array $payload)
    {
        // Dispatch event or handle logic
        SubscriptionUpdated::dispatch($payload);
    }

    /**
     * Handle a subscription not renewing event.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleSubscriptionNotRenew(array $payload)
    {
        SubscriptionUpdated::dispatch($payload);
    }

    /**
     * Handle an invoice creation event.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleInvoiceCreate(array $payload)
    {
        InvoiceCreated::dispatch($payload);
    }

    /**
     * Handle an invoice update event.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleInvoiceUpdate(array $payload)
    {
        InvoiceUpdated::dispatch($payload);
    }

    /**
     * Handle an invoice payment failure event.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleInvoicePaymentFailed(array $payload)
    {
        InvoicePaymentFailed::dispatch($payload);
    }

    /**
     * Handle a charge dispute creation event.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleChargeDisputeCreate(array $payload)
    {
        ChargeDisputeCreated::dispatch($payload);
    }

    /**
     * Handle a transfer success event.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleTransferSuccess(array $payload)
    {
        TransferSuccess::dispatch($payload);
    }

    /**
     * Handle a transfer failure event.
     *
     * @param  array  $payload
     * @return void
     */
    protected function handleTransferFailed(array $payload)
    {
        TransferFailed::dispatch($payload);
    }
}
