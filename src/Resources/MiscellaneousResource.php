<?php

namespace Turndale\Paystack\Resources;

use Turndale\Paystack\Exceptions\PaystackException;

class MiscellaneousResource extends BaseResource
{
    /**
     * Get a list of all supported banks and their properties.
     * @param array $filters ['country', 'use_cursor', 'perPage', 'pay_with_bank', etc.]
     * @return array List of bank objects
     * @throws PaystackException
     */
    public function listBanks(array $filters = []): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/bank", $filters);

        return $this->handleResponse($response);
    }

    /**
     * Get a list of countries that Paystack currently supports.
     * @return array List of country objects with currencies and features
     * @throws PaystackException
     */
    public function listCountries(): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/country");

        return $this->handleResponse($response);
    }

    /**
     * Get a list of states for a country for address verification (AVS).
     * @param string $countryCode The ISO code of the country (e.g., CA, US)
     * @return array List of states with names and abbreviations
     * @throws PaystackException
     */
    public function listStates(string $countryCode): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = $this->request()->get("$this->baseUrl/address_verification/states", [
            'country' => $countryCode
        ]);

        return $this->handleResponse($response);
    }
}