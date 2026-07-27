<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DokuService
{
    protected string $clientId = 'BRN-0240-1781073306854';
    protected string $secretKey = 'SK-gSW1xpHD8B8e7k9BTn1m';
    protected string $baseUrl = 'https://api-sandbox.doku.com';

    public function getPaymentUrl(Order $order): ?string
    {
        $fallbackUrl = 'https://sandbox.doku.com/checkout-link-v2/doku-simulation-link-' . $order->order_number;

        try {
            $requestId = (string) Str::uuid();
            $timestamp = gmdate('Y-m-d\TH:i:s\Z');
            $requestTarget = '/checkout/v1/payment';

            $body = [
                'order' => [
                    'amount' => (int) $order->total_amount,
                    'invoice_number' => $order->order_number,
                    'currency' => 'IDR',
                    'callback_url' => route('ecommerce.customer') . '?paid=1',
                    'auto_redirect' => true,
                ],
                'payment' => [
                    'payment_due_date' => 60,
                ],
                'customer' => [
                    'id' => 'CUST-' . ($order->user_id ?? 'GUEST'),
                    'name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ]
            ];

            $jsonBody = json_encode($body);
            $digest = base64_encode(hash('sha256', $jsonBody, true));

            $signatureString = "Client-Id:" . $this->clientId . "\n" .
                               "Request-Id:" . $requestId . "\n" .
                               "Request-Timestamp:" . $timestamp . "\n" .
                               "Request-Target:" . $requestTarget . "\n" .
                               "Digest:" . $digest;

            $signature = base64_encode(hash_hmac('sha256', $signatureString, $this->secretKey, true));
            $finalSignature = "HMACSHA256=" . $signature;

            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $finalSignature,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . $requestTarget, $body);

            if ($response->successful()) {
                $data = $response->json();
                return $data['response']['payment']['url'] ?? $fallbackUrl;
            }

            logger()->error('DOKU API Error Response: ' . $response->body());
        } catch (\Throwable $e) {
            logger()->error('DOKU Service failed: ' . $e->getMessage());
        }

        return $fallbackUrl;
    }
}
