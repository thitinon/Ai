<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    public function __construct(protected PaymentService $paymentService)
    {
    }

    public function createCheckout(Request $request)
    {
        $validated = $request->validate([
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'required|exists:courses,id',
            'payment_method' => 'required|in:stripe,promptpay',
        ]);

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'currency' => 'THB',
        ]);

        // Add order items
        foreach ($validated['course_ids'] as $courseId) {
            $course = \App\Models\Course::find($courseId);
            $order->items()->create([
                'course_id' => $courseId,
                'price' => $course->effective_price,
            ]);
        }

        // Update order totals
        $total = $order->items->sum('price');
        $order->update([
            'total_amount' => $total,
            'final_amount' => $total,
        ]);

        $checkoutUrl = $this->paymentService->createCheckout($order, $validated['payment_method']);

        return response()->json([
            'order_id' => $order->id,
            'checkout_url' => $checkoutUrl,
        ]);
    }

    public function confirmPayment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::find($validated['order_id']);

        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status === 'paid') {
            return response()->json(['message' => 'Order already paid'], 400);
        }

        // Mark as paid and create enrollments
        $this->paymentService->handleSuccessfulPayment($order);

        return response()->json([
            'message' => 'Payment confirmed',
            'order_id' => $order->id,
        ]);
    }
}
