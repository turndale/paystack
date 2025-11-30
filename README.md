# Laravel Paystack Package

A simple  Laravel package for integrating Paystack payments into your Laravel 11 and Above application.

## Installation

```bash
composer require hellofromsteve/paystack
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=paystack-config
```

Add your Paystack credentials to your `.env` file:

```env
PAYSTACK_SECRET_KEY=your_secret_key_here
PAYSTACK_PUBLIC_KEY=your_public_key_here
PAYSTACK_URL=https://api.paystack.co
```

## Usage

### Using the Helper Function

```php
// Get the service instance
$paystack = paystack();

// Or call methods directly
$plans = paystack('getPlans');
$transaction = paystack('initializeTransaction', [
    'email' => 'customer@example.com',
    'amount' => 10000, // amount in kobo
]);
```

### Using Dependency Injection

```php
use HelloFromSteve\Paystack\PaystackService;

class PaymentController extends Controller
{
    public function __construct(
        protected PaystackService $paystack
    ) {}

    public function initialize()
    {
        $response = $this->paystack->initializeTransaction([
            'email' => 'customer@example.com',
            'amount' => 10000,
        ]);
        
        return $response;
    }
}
```

## Available Methods

- `getPlans()` - Get all plans
- `createPlan(array $payload)` - Create a new plan
- `getTransactions()` - Get all transactions
- `initializeTransaction(array $payload)` - Initialize a transaction
- `verifyTransaction(string $reference)` - Verify a transaction
- `createCustomer(array $payload)` - Create a customer
- `chargeAuthorization(array $payload)` - Charge authorization
- `createSubscription(array $payload)` - Subscribe customer to a plan

## License

MIT

