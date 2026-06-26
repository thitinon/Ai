<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        protected StripePaymentService $stripeService,
        protected PromptPayService $promptpayService
    ) {}

    /**
     * Create checkout session for payment
     */
    public function createCheckout(Order $order, string $paymentMethod = 'stripe'): ?string
    {
        if ($paymentMethod === 'stripe') {
            $successUrl = route('payments.success');
            $cancelUrl = route('payments.cancel');
            $session = $this->stripeService->createCheckoutSession(
                $order,
                $successUrl,
                $cancelUrl
            );
            return $session->url;
        } elseif ($paymentMethod === 'promptpay') {
            return $this->promptpayService->generateQRCode($order);
        }

        return null;
    }

    /**
     * Handle successful payment and create enrollments
     */
    public function handleSuccessfulPayment(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            // Create enrollments for all items in order
            foreach ($order->items as $item) {
                Enrollment::create([
                    'user_id' => $order->user_id,
                    'course_id' => $item->course_id,
                    'payment_id' => $order->id,
                    'enrolled_at' => now(),
                ]);
            }

            // Fire payment completed event
            event(new \App\Events\PaymentCompleted($order));

            return true;
        });
    }

    /**
     * Process refund
     */
    public function processRefund(Order $order, ?float $amount = null): bool
    {
        if ($order->payment_method === 'stripe') {
            return $this->stripeService->createRefund($order, $amount);
        } elseif ($order->payment_method === 'promptpay') {
            // Implement PromptPay refund if supported by provider
            return true;
        }

        return false;
    }
}
