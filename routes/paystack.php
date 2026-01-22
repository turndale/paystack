<?php

use Illuminate\Support\Facades\Route;
use Turndale\Paystack\Http\Controllers\WebhookController;
use Turndale\Paystack\Middleware\VerifyPaystackWebhook;

Route::post(config('paystack.webhook_path', 'paystack/webhook'), [WebhookController::class, 'handleWebhook'])
    ->name('paystack.webhook')
    ->middleware(VerifyPaystackWebhook::class);
