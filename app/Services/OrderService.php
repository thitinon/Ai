<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {}

    public function getUserOrders(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->getByUser($userId, $perPage);
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function createOrder(int $userId, array $items, $couponId = null): Order
    {
        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += $item['price'];
        }

        $discountAmount = 0;
        if ($couponId) {
            // Apply coupon discount logic
        }

        $finalAmount = $totalAmount - $discountAmount;

        return $this->orderRepository->create([
            'user_id' => $userId,
            'total_amount' => $totalAmount,
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
            'currency' => 'THB',
            'status' => 'pending',
            'coupon_id' => $couponId,
        ]);
    }

    public function markAsPaid(int $orderId, string $paymentMethod, string $paymentRef): Order
    {
        return $this->orderRepository->update($orderId, [
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_ref' => $paymentRef,
            'paid_at' => now(),
        ]);
    }

    public function getPendingOrders(): Collection
    {
        return $this->orderRepository->getPendingOrders();
    }

    public function getTotalRevenue(): float
    {
        return $this->orderRepository->getTotalRevenue();
    }

    public function getRevenueByDateRange(\DateTime $start, \DateTime $end): float
    {
        return $this->orderRepository->getRevenueByDateRange($start, $end);
    }
}
