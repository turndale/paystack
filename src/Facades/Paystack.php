<?php


namespace Turndale\Paystack\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Turndale\Paystack\Resources\TransactionResource transaction()
 * @method static \Turndale\Paystack\Resources\PlanResource plan()
 * @method static \Turndale\Paystack\Resources\SubscriptionResource subscription()
 * @method static \Turndale\Paystack\Resources\CustomerResource customer()
 * @method static \Turndale\Paystack\Resources\PageResource page()
 * @method static \Turndale\Paystack\Resources\ProductResource product()
 * @method static \Turndale\Paystack\Resources\PaymentRequestResource paymentRequest()
 * @method static \Turndale\Paystack\Resources\SettlementResource settlement()
 * @method static \Turndale\Paystack\Resources\TransferRecipientResource transferRecipient()
 * @method static \Turndale\Paystack\Resources\TransferResource transfer()
 * @method static \Turndale\Paystack\Resources\TransferControlResource transferControl()
 * @method static \Turndale\Paystack\Resources\ChargeResource charge()
 * @method static \Turndale\Paystack\Resources\DisputeResource dispute()
 * @method static \Turndale\Paystack\Resources\RefundResource refund()
 * @method static \Turndale\Paystack\Resources\VerificationResource verification()
 * @method static \Turndale\Paystack\Resources\MiscellaneousResource miscellaneous()
 * @method static \Turndale\Paystack\Resources\TransactionSplitResource transactionSplit()
 * @method static \Turndale\Paystack\Resources\SubaccountResource subaccount()
 * 
 *
 *  @see \Turndale\Paystack\PaystackService
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