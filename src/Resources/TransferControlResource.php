<?php

namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class TransferControlResource extends BaseResource
{
    /**
     * Fetch the available balance on your integration.
     * * @return array List of balances per currency
     * @throws PaystackException
     */
    public function checkBalance(): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/balance");

        return $this->handleResponse($response);
    }

    /**
     * Fetch all pay-ins and pay-outs that occurred on your integration.
     * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array Each entry: id, domain, balance (the running balance after this entry, in the currency's
     *   smallest unit), currency, difference (the signed change; negative for a pay-out), reason (often
     *   empty), model_responsible (e.g. "Transfer", "Transaction", "Refund"), model_row (that record's ID),
     *   createdAt, updatedAt.
     * @throws PaystackException
     */
    public function fetchLedger(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/balance/ledger", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Generates a new OTP and sends it to the business phone number.
     * @param string $transferCode The code for the transfer
     * @param string $reason Either 'resend_otp' or 'transfer'
     * @return array Only status/message, e.g. "OTP has been resent".
     * @throws PaystackException
     */
    public function resendOtp(string $transferCode, string $reason = 'resend_otp'): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transfer/resend_otp", [
            'transfer_code' => $transferCode,
            'reason' => $reason
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Request to disable the OTP requirement for transfers.
     * Paystack will send an OTP to the business phone to verify this request.
     * @return array Only status/message, e.g. "OTP has been sent to mobile number ending with 4321".
     * @throws PaystackException
     */
    public function disableOtpRequest(): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transfer/disable_otp");

        return $this->handleResponse($response);
    }

    /**
     * Finalize the request to disable OTP on your transfers.
     * @param string $otp The OTP sent to the business phone
     * @return array Only status/message.
     * @throws PaystackException
     */
    public function disableOtpFinalize(string $otp): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transfer/disable_otp_finalize", [
            'otp' => $otp
        ]);

        return $this->handleResponse($response);
    }

    /**
     * Re-enable the OTP requirement for transfers.
     * @return array Only status/message.
     * @throws PaystackException
     */
    public function enableOtp(): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/transfer/enable_otp");

        return $this->handleResponse($response);
    }
}