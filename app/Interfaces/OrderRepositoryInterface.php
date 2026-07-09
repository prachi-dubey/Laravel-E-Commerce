<?php

namespace App\Interfaces;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function paginateForUser(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function paginateAll(int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): ?Order;

    public function create(array $data, array $items): Order;

    public function updateStatus(Order $order, string $status): Order;
}
