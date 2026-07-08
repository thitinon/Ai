<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\StripePaymentService;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function __construct(protected StripePaymentService $stripeService)
    {
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig, $secret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationFailureException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;
            case 'charge.refunded':
                $this->handleRefund($event->data->object);
                break;
        }

        return response()->json(['success' => true]);
    }

    protected function handleCheckoutCompleted($session): void
    {
        $order = $this->stripeService->handleSuccessfulPayment($session->id);

        if ($order) {
            // Create enrollments
            event(new \App\Events\PaymentCompleted($order));
        }
    }

    protected function handleRefund($charge): void
    {
        $order = Order::where('payment_ref', $charge->payment_intent)->first();

        if ($order) {
            $order->update(['status' => 'refunded']);
            event(new \App\Events\PaymentRefunded($order));
        }
    }
}
