<?php

namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class TransferControlResource extends BaseResource
{
    /**
     * Fetch the available balance on your integration.
     * * @return array List of balances per currency
     * @throws PaystackException|ConnectionException
     */
    public function checkBalance(): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/balance")
        );
    }

    /**
     * Fetch all pay-ins and pay-outs that occurred on your integration.
     * * @param array $filters ['perPage', 'page', 'from', 'to']
     * @return array The balance ledger data
     * @throws PaystackException|ConnectionException
     */
    public function fetchLedger(array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/balance/ledger", $filters)
        );
    }

    /**
     * Generates a new OTP and sends it to the business phone number.
     * * @param string $transferCode The code for the transfer
     * @param string $reason Either 'resend_otp' or 'transfer'
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function resendOtp(string $transferCode, string $reason = 'resend_otp'): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/transfer/resend_otp", [
                'transfer_code' => $transferCode,
                'reason' => $reason
            ])
        );
    }

    /**
     * Request to disable the OTP requirement for transfers.
     * Paystack will send an OTP to the business phone to verify this request.
     * * @return array
     * @throws PaystackException|ConnectionException
     */
    public function disableOtpRequest(): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/transfer/disable_otp")
        );
    }

    /**
     * Finalize the request to disable OTP on your transfers.
     * * @param string $otp The OTP sent to the business phone
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function disableOtpFinalize(string $otp): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/transfer/disable_otp_finalize", [
                'otp' => $otp
            ])
        );
    }

    /**
     * Re-enable the OTP requirement for transfers.
     * * @return array
     * @throws PaystackException|ConnectionException
     */
    public function enableOtp(): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/transfer/enable_otp")
        );
    }
}