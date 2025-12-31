<?php

use Illuminate\Support\Facades\Route;
use StephenAsare\Paystack\Http\Controllers\WebhookController;
use StephenAsare\Paystack\Middleware\VerifyPaystackWebhook;

Route::post(config('paystack.webhook_path', 'paystack/webhook'), [WebhookController::class, 'handleWebhook'])
    ->name('paystack.webhook')
    ->middleware(VerifyPaystackWebhook::class);
