<?php

namespace App\Interfaces;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function paginateForUser(int $userId, ?int $perPage = null): LengthAwarePaginator;

    public function paginateAll(?int $perPage = null): LengthAwarePaginator;

    public function findById(int $id): ?Order;

    public function create(array $data, array $items): Order;

    public function updateStatus(Order $order, string $status): Order;
}
