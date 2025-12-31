<?php

namespace StephenAsare\Paystack;

use Illuminate\Support\ServiceProvider;

class PaystackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/paystack.php', 'paystack');

        $this->app->singleton('paystack', function ($app) {
            $secret = config('paystack.secret');
            $baseUrl = config('paystack.url', 'https://api.paystack.co');

            // Safety Check: Ensure the secret is actually set
            if (!$secret) {
                throw new \RuntimeException(
                    "Paystack secret key is not set. Please add PAYSTACK_SECRET_KEY to your .env file."
                );
            }

            return new PaystackService($secret, $baseUrl);
        });

        // Add an alias so developers can Type-hint the class in constructors
        $this->app->alias('paystack', \StephenAsare\Paystack\PaystackService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/paystack.php');

        // Check if the application is running in the console (terminal)
    // There is no need to register publishing logic for web requests
        if ($this->app->runningInConsole()) {
            
            $this->publishes([
                __DIR__.'/../config/paystack.php' => config_path('paystack.php'),
            ], 'paystack-config');

            $this->publishes([
                __DIR__.'/database/migrations' => database_path('migrations'),
            ], 'paystack-migrations');

            $this->commands([
                Console\InstallCommand::class,
            ]);

            // Optional : Load migrations 
            $this->loadMigrationsFrom(realpath(__DIR__.'/database/migrations'));
        }


        $this->app['router']->aliasMiddleware('paystack.webhook', \StephenAsare\Paystack\Middleware\VerifyPaystackWebhook::class);
    }
}


