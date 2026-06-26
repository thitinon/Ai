<?php

namespace App\Services\Payment;

use App\Models\Order;

class PromptPayService
{
    /**
     * Generate PromptPay QR code for payment
     * Integrate with Thai banking API (e.g., Omise, 2C2P, Paysbuy)
     */
    public function generateQRCode(Order $order): string
    {
        // Example: integrate with a Thai payment gateway
        // This is a placeholder - actual implementation depends on chosen provider

        $merchantId = config('services.promptpay.merchant_id');
        $phone = config('services.promptpay.phone');
        $amount = $order->final_amount;

        // Call Thai payment gateway API to generate QR
        // Return QR image URL or data

        return sprintf(
            'promptpay://pay?phone=%s&amount=%s&ref=%s',
            $phone,
            $amount,
            $order->id
        );
    }

    /**
     * Verify payment via webhook from payment provider
     */
    public function verifyPayment(string $orderId, array $webhookData): bool
    {
        $order = Order::find($orderId);

        if (! $order) {
            return false;
        }

        // Verify signature and status from webhook
        if ($this->verifyWebhookSignature($webhookData)) {
            $order->update([
                'status' => 'paid',
                'payment_method' => 'promptpay',
                'payment_ref' => $webhookData['transaction_id'] ?? null,
                'paid_at' => now(),
            ]);

            return true;
        }

        return false;
    }

    /**
     * Verify webhook signature for security
     */
    private function verifyWebhookSignature(array $webhookData): bool
    {
        $secret = config('services.promptpay.webhook_secret');
        $signature = $webhookData['signature'] ?? null;

        if (! $signature) {
            return false;
        }

        $payload = json_encode($webhookData);
        $hash = hash_hmac('sha256', $payload, $secret);

        return hash_equals($hash, $signature);
    }
}
