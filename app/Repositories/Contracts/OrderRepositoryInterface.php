<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function getByUser(int $userId, int $perPage = 10): LengthAwarePaginator;
    public function findById(int $id): ?Order;
    public function create(array $data): Order;
    public function update(int $id, array $data): Order;
    public function getPaidOrders(int $perPage = 10): LengthAwarePaginator;
    public function getPendingOrders(): \Illuminate\Database\Eloquent\Collection;
    public function getTotalRevenue(): float;
    public function getRevenueByDateRange(\DateTime $start, \DateTime $end): float;
}
