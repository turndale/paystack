<?php


namespace HelloFromSteve\Paystack\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \HelloFromSteve\Paystack\Resources\TransactionResource transactions()
 * @method static \HelloFromSteve\Paystack\Resources\PlanResource plans()
 * @method static \HelloFromSteve\Paystack\Resources\SubscriptionResource subscriptions()
 * @method static \HelloFromSteve\Paystack\Resources\CustomerResource customers()
 * @method static \HelloFromSteve\Paystack\Resources\PageResource pages()
 * @method static \HelloFromSteve\Paystack\Resources\ProductResource products()
 * @method static \HelloFromSteve\Paystack\Resources\PaymentRequestResource paymentRequests()
 * @method static \HelloFromSteve\Paystack\Resources\SettlementResource settlements()
 * @method static \HelloFromSteve\Paystack\Resources\TransferRecipientResource transferRecipients()
 * @method static \HelloFromSteve\Paystack\Resources\TransferResource transfers()
 * @method static \HelloFromSteve\Paystack\Resources\TransferControlResource transferControl()
 * @method static \HelloFromSteve\Paystack\Resources\ChargeResource charges()
 * @method static \HelloFromSteve\Paystack\Resources\DisputeResource disputes()
 * @method static \HelloFromSteve\Paystack\Resources\RefundResource refunds()
 * @method static \HelloFromSteve\Paystack\Resources\VerificationResource verification()
 * @method static \HelloFromSteve\Paystack\Resources\MiscellaneousResource miscellaneous()
 *
 *  @see \HelloFromSteve\Paystack\PaystackService
 */
class Paystack extends Facade
{
    /**
     * Get the registered name of the component.
     * * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'paystack';
    }
}