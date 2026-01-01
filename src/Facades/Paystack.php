<?php


namespace StephenAsare\Paystack\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \StephenAsare\Paystack\Resources\TransactionResource transaction()
 * @method static \StephenAsare\Paystack\Resources\PlanResource plan()
 * @method static \StephenAsare\Paystack\Resources\SubscriptionResource subscription()
 * @method static \StephenAsare\Paystack\Resources\CustomerResource customer()
 * @method static \StephenAsare\Paystack\Resources\PageResource page()
 * @method static \StephenAsare\Paystack\Resources\ProductResource product()
 * @method static \StephenAsare\Paystack\Resources\PaymentRequestResource paymentRequest()
 * @method static \StephenAsare\Paystack\Resources\SettlementResource settlement()
 * @method static \StephenAsare\Paystack\Resources\TransferRecipientResource transferRecipient()
 * @method static \StephenAsare\Paystack\Resources\TransferResource transfer()
 * @method static \StephenAsare\Paystack\Resources\TransferControlResource transferControl()
 * @method static \StephenAsare\Paystack\Resources\ChargeResource charge()
 * @method static \StephenAsare\Paystack\Resources\DisputeResource dispute()
 * @method static \StephenAsare\Paystack\Resources\RefundResource refund()
 * @method static \StephenAsare\Paystack\Resources\VerificationResource verification()
 * @method static \StephenAsare\Paystack\Resources\MiscellaneousResource miscellaneous()
 * @method static \StephenAsare\Paystack\Resources\SplitResource split()
 * @method static \StephenAsare\Paystack\Resources\SubaccountResource subaccount()
 * 
 *
 *  @see \StephenAsare\Paystack\PaystackService
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