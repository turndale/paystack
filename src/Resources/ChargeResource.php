<?php

namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;

class ChargeResource extends BaseResource
{
    /**
     * Initiate a payment by integrating the payment channel of your choice.
     * @param array $payload ['email', 'amount', 'bank', 'mobile_money', 'ussd', 'authorization_code', etc.]
     * @return array Charge status (success, send_otp, send_pin, send_birthday, etc.)
     * @throws PaystackException
     */
    public function create(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = (int) $payload['amount'];
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/charge", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Submit PIN to continue a charge.
     * @param string $pin 4-digit card PIN
     * @param string $reference Transaction reference
     * @return array
     * @throws PaystackException
     */
    public function submitPin(string $pin, string $reference): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/charge/submit_pin", [
            'pin' => $pin,
            'reference' => $reference
        ]);
        return $this->handleResponse($response);
    }

    /**
     * Submit OTP to complete a charge.
     * @param string $otp One-time password submitted by user
     * @param string $reference Transaction reference
     * @return array
     * @throws PaystackException
     */
    public function submitOtp(string $otp, string $reference): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/charge/submit_otp", [
            'otp' => $otp,
            'reference' => $reference
        ]);
        return $this->handleResponse($response);
    }

    /**
     * Submit Birthday when requested (Required by some banks for extra security).
     * @param string $birthday Format: YYYY-MM-DD
     * @param string $reference
     * @return array
     * @throws PaystackException
     */
    public function submitBirthday(string $birthday, string $reference): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/charge/submit_birthday", [
            'birthday' => $birthday,
            'reference' => $reference
        ]);
        return $this->handleResponse($response);
    }

    /**
     * Submit address details when requested.
     * @param array $payload ['address', 'city', 'state', 'zip_code', 'reference']
     * @return array
     * @throws PaystackException
     */
    public function submitAddress(array $payload): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/charge/submit_address", $payload);
        return $this->handleResponse($response);
    }

    /**
     * Check the status of a pending charge.
     * Use this if you get a 'pending' status or if an exception occurs during charge.
     * @param string $reference
     * @return array
     * @throws PaystackException
     */
    public function checkStatus(string $reference): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/charge/$reference");
        return $this->handleResponse($response);
    }


    /**
     * Submit phone number when requested.
     * @param string $phone Phone number submitted by user
     * @param string $reference Transaction reference for the ongoing charge
     * @return array
     * @throws PaystackException
     */
    public function submitPhone(string $phone, string $reference): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->post("$this->baseUrl/charge/submit_phone", [
            'phone' => $phone,
            'reference' => $reference
        ]);
        return $this->handleResponse($response);
    }
}