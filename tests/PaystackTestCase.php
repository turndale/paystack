<?php

namespace Turndale\Paystack\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Turndale\Paystack\PaystackServiceProvider;

class PaystackTestCase extends Orchestra
{
    /**
     * Define environment setup.
     * This runs BEFORE the Service Providers are registered.
     */
    protected function defineEnvironment($app): void
    {
        // Set the config values exactly how your ServiceProvider expects them
        $app['config']->set('paystack.secret', 'sk_test_mock_123');
        $app['config']->set('paystack.public', 'pk_test_mock_123');
        $app['config']->set('paystack.url', 'https://api.paystack.co');

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }


//    protected function defineDatabaseMigrations(): void
////    {
////        // Realpath ensures the path is absolute and correct
////        // $this->loadMigrationsFrom(realpath(__DIR__ . '/../src/database/migrations'));
////    }

    protected function getPackageProviders($app): array
    {
        return [
            PaystackServiceProvider::class,
        ];
    }
}