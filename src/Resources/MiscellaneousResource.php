<?php

namespace StephenAsare\Paystack\Resources;

use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Http\Client\ConnectionException;

class MiscellaneousResource extends BaseResource
{
    /**
     * Get a list of all supported banks and their properties.
     * * @param array $filters ['country', 'use_cursor', 'perPage', 'pay_with_bank', etc.]
     * @return array List of bank objects
     * @throws PaystackException|ConnectionException
     */
    public function listBanks(array $filters = []): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/bank", $filters)
        );
    }

    /**
     * Get a list of countries that Paystack currently supports.
     * * @return array List of country objects with currencies and features
     * @throws PaystackException|ConnectionException
     */
    public function listCountries(): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/country")
        );
    }

    /**
     * Get a list of states for a country for address verification (AVS).
     * * @param string $countryCode The ISO code of the country (e.g., CA, US)
     * @return array List of states with names and abbreviations
     * @throws PaystackException|ConnectionException
     */
    public function listStates(string $countryCode): array
    {
        return $this->handleResponse(
            $this->request()->get("$this->baseUrl/address_verification/states", [
                'country' => $countryCode
            ])
        );
    }
}