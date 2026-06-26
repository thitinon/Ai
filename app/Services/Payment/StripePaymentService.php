<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\OrderItem;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Order $order, string $successUrl, string $cancelUrl): Session
    {
        $lineItems = $order->items->map(function (OrderItem $item) {
            return [
                'price_data' => [
                    'currency' => 'thb',
                    'product_data' => [
                        'name' => $item->course->title,
                        'description' => $item->course->subtitle,
                    ],
                    'unit_amount' => (int) ($item->price * 100), // amount in cents
                ],
                'quantity' => 1,
            ];
        })->toArray();

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'customer_email' => $order->user->email,
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ],
        ]);
    }

    public function retrieveSession(string $sessionId): Session
    {
        return Session::retrieve($sessionId);
    }

    public function handleSuccessfulPayment(string $sessionId): ?Order
    {
        $session = $this->retrieveSession($sessionId);

        if ($session->payment_status !== 'paid') {
            return null;
        }

        $orderId = $session->metadata->order_id;
        $order = Order::find($orderId);

        if (! $order) {
            return null;
        }

        $order->update([
            'status' => 'paid',
            'payment_method' => 'stripe',
            'payment_ref' => $session->payment_intent,
            'paid_at' => now(),
        ]);

        return $order;
    }

    public function createRefund(Order $order, ?float $amount = null): bool
    {
        if (! $order->payment_ref) {
            return false;
        }

        try {
            \Stripe\Refund::create([
                'payment_intent' => $order->payment_ref,
                'amount' => $amount ? (int) ($amount * 100) : null,
            ]);

            $order->update(['status' => 'refunded']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
