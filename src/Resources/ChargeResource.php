<?php

namespace HelloFromSteve\Paystack\Resources;

use HelloFromSteve\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class ChargeResource extends BaseResource
{
    /**
     * Initiate a payment by integrating the payment channel of your choice.
     * * @param array $payload ['email', 'amount', 'bank', 'mobile_money', 'ussd', 'authorization_code', etc.]
     * @return array Charge status (success, send_otp, send_pin, send_birthday, etc.)
     * @throws PaystackException
     * @throws ConnectionException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/charge", $payload)
        );
    }

    /**
     * Submit PIN to continue a charge.
     * * @param string $pin 4-digit card PIN
     * @param string $reference Transaction reference
     * @return array
     * @throws PaystackException
     * @throws ConnectionException
     */
    public function submitPin(string $pin, string $reference): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/charge/submit_pin", [
                'pin' => $pin,
                'reference' => $reference
            ])
        );
    }

    /**
     * Submit OTP to complete a charge.
     * * @param string $otp One-time password submitted by user
     * @param string $reference Transaction reference
     * @return array
     * @throws PaystackException
     * @throws ConnectionException
     */
    public function submitOtp(string $otp, string $reference): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/charge/submit_otp", [
                'otp' => $otp,
                'reference' => $reference
            ])
        );
    }

    /**
     * Submit Birthday when requested (Required by some banks for extra security).
     * * @param string $birthday Format: YYYY-MM-DD
     * @param string $reference
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function submitBirthday(string $birthday, string $reference): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/charge/submit_birthday", [
                'birthday' => $birthday,
                'reference' => $reference
            ])
        );
    }

    /**
     * Submit address details when requested.
     * * @param array $payload ['address', 'city', 'state', 'zip_code', 'reference']
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function submitAddress(array $payload): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/charge/submit_address", $payload)
        );
    }

    /**
     * Check the status of a pending charge.
     * Use this if you get a 'pending' status or if an exception occurs during charge.
     * * @param string $reference
     * @return array
     * @throws PaystackException|ConnectionException
     */
    public function checkStatus(string $reference): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/charge/$reference")
        );
    }
}