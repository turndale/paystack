<?php


namespace HelloFromSteve\Paystack;

use HelloFromSteve\Paystack\Resources\TransactionResource;
use HelloFromSteve\Paystack\Resources\PlanResource;
use HelloFromSteve\Paystack\Resources\SubscriptionResource;
use HelloFromSteve\Paystack\Resources\CustomerResource;
use HelloFromSteve\Paystack\Resources\PageResource;
use HelloFromSteve\Paystack\Resources\ProductResource;
use HelloFromSteve\Paystack\Resources\PaymentRequestResource;
use HelloFromSteve\Paystack\Resources\SettlementResource;
use HelloFromSteve\Paystack\Resources\TransferRecipientResource;
use HelloFromSteve\Paystack\Resources\TransferResource;
use HelloFromSteve\Paystack\Resources\TransferControlResource;
use HelloFromSteve\Paystack\Resources\ChargeResource;
use HelloFromSteve\Paystack\Resources\DisputeResource;
use HelloFromSteve\Paystack\Resources\RefundResource;
use HelloFromSteve\Paystack\Resources\VerificationResource;
use HelloFromSteve\Paystack\Resources\MiscellaneousResource;



class PaystackService
{
    public function __construct(
        protected string $secret,
        protected string $baseUrl = 'https://api.paystack.co'
    ) {}
/**
     * Manage payment transactions, initializations, and verifications.
     * * @return TransactionResource
     */
    public function transaction(): TransactionResource
    {
        return new TransactionResource($this->secret, $this->baseUrl);
    }

    /**
     * Manage subscription plans and pricing tiers for your schools.
     * * @return PlanResource
     */
    public function plan(): PlanResource
    {
        return new PlanResource($this->secret, $this->baseUrl);
    }

    /**
     * Manage recurring billing and subscription lifecycles.
     * * @return SubscriptionResource
     */
    public function subscription(): SubscriptionResource
    {
        return new SubscriptionResource($this->secret, $this->baseUrl);
    }

    /**
     * Manage customer profiles and identity verification.
     * * @return CustomerResource
     */
    public function customer(): CustomerResource
    {
        return new CustomerResource($this->secret, $this->baseUrl);
    }

    /**
     * Manage custom payment pages for event-based collections.
     * * @return PageResource
     */
    public function page(): PageResource
    {
        return new PageResource($this->secret, $this->baseUrl);
    }

    /**
     * Manage physical or digital products and inventory.
     * * @return ProductResource
     */
    public function product(): ProductResource
    {
        return new ProductResource($this->secret, $this->baseUrl);
    }

    /**
     * Manage professional itemized invoices and direct payment requests.
     * * @return PaymentRequestResource
     */
    public function paymentRequest(): PaymentRequestResource
    {
        return new PaymentRequestResource($this->secret, $this->baseUrl);
    }

    /**
     * Gain insights into payouts made by Paystack to your bank account.
     * * @return SettlementResource
     */
    public function settlement(): SettlementResource
    {
        return new SettlementResource($this->secret, $this->baseUrl);
    }

    /**
     * Automate sending money to customers, teachers, or vendors.
     * * @return TransferResource
     */
    public function transfer(): TransferResource
    {
        return new TransferResource($this->secret, $this->baseUrl);
    }



    /**
     * Manage transfer settings, check balances, and control OTP requirements.
     * * @return TransferControlResource
     */
    public function transferControl(): TransferControlResource
    {
        return new TransferControlResource($this->secret, $this->baseUrl);
    }


    /**
     * Configure and initiate custom payment channels (MoMo, USSD, Direct Bank).
     * * @return ChargeResource
     */
    public function charges(): ChargeResource
    {
        return new ChargeResource($this->secret, $this->baseUrl);
    }



    /**
     * Manage transaction disputes and provide evidence for resolutions.
     * * @return DisputeResource
     */
    public function dispute(): DisputeResource
    {
        return new DisputeResource($this->secret, $this->baseUrl);
    }


 
    /**
     * Manage beneficiaries for outbound transfers.
     * * @return TransferRecipientResource
     */
    public function transferRecipient(): TransferRecipientResource
    {
        return new TransferRecipientResource($this->secret, $this->baseUrl);
    }



    /**
     * Manage transaction refunds and reversals.
     * * @return RefundResource
     */
    public function refund(): RefundResource
    {
        return new RefundResource($this->secret, $this->baseUrl);
    }


    /**
     * Perform KYC processes, verify bank accounts, and resolve card BINs.
     * * @return VerificationResource
     */
    public function verification(): VerificationResource
    {
        return new VerificationResource($this->secret, $this->baseUrl);
    }


    /**
     * Access supporting data like bank lists, country codes, and AVS states.
     * * @return MiscellaneousResource
     */
    public function miscellaneous(): MiscellaneousResource
    {
        return new MiscellaneousResource($this->secret, $this->baseUrl);
    }
}