<?php

namespace App\Interfaces;

use App\Constants\Pagination;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function paginateForUser(int $userId, int $perPage = Pagination::DEFAULT_PER_PAGE): LengthAwarePaginator;

    public function paginateAll(int $perPage = Pagination::DEFAULT_PER_PAGE): LengthAwarePaginator;

    public function findById(int $id): ?Order;

    public function create(array $data, array $items): Order;

    public function updateStatus(Order $order, string $status): Order;
}
