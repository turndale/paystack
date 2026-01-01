# Laravel Paystack

A robust, Cashier-like Paystack integration package for Laravel 11+. This package provides an expressive, fluent interface to Paystack's subscription billing services, handling boilerplate subscription code so you can focus on building your application.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/stephenasaredev/paystack.svg?style=flat-square)](https://packagist.org/packages/stephenasaredev/paystack)
[![Total Downloads](https://img.shields.io/packagist/dt/stephenasaredev/paystack.svg?style=flat-square)](https://packagist.org/packages/stephenasaredev/paystack)
[![License](https://img.shields.io/packagist/l/stephenasaredev/paystack.svg?style=flat-square&kill_cache=1)](https://packagist.org/packages/stephenasaredev/paystack)

## Documentation

For full documentation, usage guides, and API reference, please visit:

**<a href="https://paystack.stephenasare.dev/" target="_blank">https://paystack.stephenasare.dev/</a>**

---

## Features

- **Subscription Management**: Fluent API for creating, cancelling, and resuming subscriptions.
- **Webhooks**: Automatic handling of Paystack webhooks (payments, subscriptions, invoices).
- **Trial Periods**: Built-in support for trial periods on subscriptions.
- **Grace Periods**: Handle subscription expiration gracefully.
- **Invoices**: Access and manage invoices directly.
- **Generic Payment Support**: Support for Mobile Money and other payment channels.
- **Testing**: Fully tested with PHPUnit and Orchestra Testbench.

## Requirements

- PHP 8.1+
- Laravel 11.0+

## Installation

You can install the package via composer:

```bash
composer require stephenasaredev/paystack
```

After installing, run the installation command to publish the configuration and migrations:

```bash
php artisan paystack:install
```

This will:
1. Publish `config/paystack.php`
2. Publish database migrations
3. Ask if you want to run the migrations immediately

## Quick Start

### 1. Setup Billable Model

Add the `Billable` trait to your User model:

```php
use StephenAsare\Paystack\Traits\HasPaystack;
use StephenAsare\Paystack\Contracts\Billable;

class User extends Authenticatable implements Billable
{
    use HasPaystack;
}
```

### 2. Create a Subscription

```php
$user = User::find(1);

// Create a subscription with a trial period
$user->newSubscription('default', 'PLN_gx2wn530m0i3w3m')
    ->trialDays(7)
    ->create($authCode);
```

### 3. Check Subscription Status

```php
if ($user->subscribed('default')) {
    // User has an active subscription...
}

if ($user->onTrial('default')) {
    // User is on a trial period...
}
```

## Webhooks

The package automatically handles Paystack webhooks for you. Just set up your webhook URL in the Paystack Dashboard to point to:

`https://your-domain.com/paystack/webhook`

You can listen to events in your application:

```php
use StephenAsare\Paystack\Events\PaymentSuccess;

Event::listen(PaymentSuccess::class, function ($event) {
    // Handle successful payment...
});
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Credits

- [Stephen Asare](https://github.com/stephenasare)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

MIT

## Support

If you find this package helpful, please consider:
-  Starring the repository on [GitHub](https://github.com/stephenasaredev/paystack)
-  Following me on [Twitter/X](https://x.com/stephenasare1)

