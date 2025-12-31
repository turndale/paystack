<?php

namespace StephenAsare\Paystack\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyPaystackWebhook
{
    /**
     * Handle an incoming webhook request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. IP Whitelisting Check
        $allowedIps = array_filter(config('paystack.white_listed_ips', []));
        
        // Note: In local development, you might want to skip IP checks 
        // because your tunnel (ngrok) IP will not match Paystack's IPs.
        if (app()->isProduction() && !empty($allowedIps)) {
            if (!in_array($request->ip(), $allowedIps)) {
                return response()->json(['message' => 'Unauthorized IP source'], 401);
            }
        }

        // 2. Signature Verification
        if (!$request->hasHeader('x-paystack-signature')) {
            return response()->json(['message' => 'Missing Paystack signature header'], 400);
        }

        $payload = $request->getContent();
        $secret = config('paystack.secret');
        
        // Calculate the hash
        $expectedSignature = hash_hmac('sha512', $payload, $secret);

        if ($request->header('x-paystack-signature') !== $expectedSignature) {
            return response()->json(['message' => 'Invalid webhook signature'], 401);
        }

        return $next($request);
    }
}