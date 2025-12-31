<?php

namespace StephenAsare\Paystack\Concerns;

use StephenAsare\Paystack\Models\PaystackCustomer;
use StephenAsare\Paystack\Exceptions\PaystackException;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\Client\ConnectionException;

trait ManagesCustomer
{
    /**
     * Get the customer relationship.
     */
    public function paystackCustomer(): MorphOne
    {
        return $this->morphOne(PaystackCustomer::class, 'billable');
    }

    /**
     * Create a Paystack customer for the billable model.
     * @throws PaystackException|ConnectionException
     */
    public function createAsPaystackCustomer(array $options = []): PaystackCustomer
    {
        if ($this->paystackCustomer) {
            return $this->paystackCustomer;
        }

        if (! in_array('email', $this->fillable) && ! isset($this->email)) {
             throw new PaystackException("The billable model must have an email attribute.");
        }

        $payload = array_merge([
            'email' => $this->email,
            'first_name' => $this->first_name ?? null,
            'last_name' => $this->last_name ?? null,
            'phone' => $this->phone ?? null,
        ], $options);

        $response = app('paystack')->customer()->create($payload);

        if (! ($response['status'] ?? false)) {
            throw new PaystackException(
                message: "Paystack Customer Creation Failed: " . ($response['message'] ?? 'Unknown error'),
                httpCode: 400
            );
        }

        $customerData = $response['data'];

        return $this->paystackCustomer()->create([
            'paystack_id' => $customerData['customer_code'],
            'email' => $customerData['email'],
        ]);
    }

    /**
     * Find the billable model by Paystack Customer ID.
     */
    public static function findByPaystackId(string $id)
    {
        return new PaystackCustomer()->where('paystack_id', $id)->first()?->billable;
    }
}
