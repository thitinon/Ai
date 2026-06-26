<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function getByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('user_id', $userId)
            ->with('items', 'coupon')
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Order
    {
        return Order::with('items.course', 'user', 'coupon')
            ->find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(int $id, array $data): Order
    {
        $order = Order::find($id);
        $order->update($data);
        return $order->refresh();
    }

    public function getPaidOrders(int $perPage = 10): LengthAwarePaginator
    {
        return Order::paid()
            ->with('user', 'items')
            ->latest('paid_at')
            ->paginate($perPage);
    }

    public function getPendingOrders(): Collection
    {
        return Order::where('status', 'pending')
            ->with('user', 'items')
            ->get();
    }

    public function getTotalRevenue(): float
    {
        return (float) Order::paid()
            ->sum('final_amount');
    }

    public function getRevenueByDateRange(\DateTime $start, \DateTime $end): float
    {
        return (float) Order::paid()
            ->whereBetween('paid_at', [$start, $end])
            ->sum('final_amount');
    }
}
