<?php

namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class VerificationResource extends BaseResource
{
    /**
     * Confirm that an account number belongs to the expected customer.
     * Useful for verifying staff bank details before saving them.
     * * @param string $accountNumber
     * @param string $bankCode
     * @return array Contains account_name and account_number
     * @throws PaystackException|ConnectionException
     */
    public function resolveAccount(string $accountNumber, string $bankCode): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/bank/resolve", [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode
            ])
        );
    }

    /**
     * Confirm the authenticity of a customer's account details (KYC).
     * This is a more rigorous check often used in South Africa.
     * * @param array $payload ['account_name', 'account_number', 'account_type', 'bank_code', 'country_code', 'document_type', 'document_number']
     * @return array Verification status and message
     * @throws PaystackException|ConnectionException
     */
    public function validateAccount(array $payload): array
    {
        return $this->handleResponse(
            $this->request()->post("$this->baseUrl/bank/validate", $payload)
        );
    }

    /**
     * Get information about a card using its first 6 digits (BIN).
     * Helps identify card brand, type, and issuing country.
     * * @param string $bin First 6 characters of the card
     * @return array Card details including bank and brand
     * @throws PaystackException|ConnectionException
     */
    public function resolveCardBin(string $bin): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/decision/bin/$bin")
        );
    }
}