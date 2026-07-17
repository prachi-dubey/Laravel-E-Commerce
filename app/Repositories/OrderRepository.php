<?php

namespace App\Repositories;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function paginateForUser(int $userId, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('constants.pagination.default_per_page');

        return Order::with(['items.product'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function paginateAll(?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('constants.pagination.default_per_page');

        return Order::with(['items.product', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Order
    {
        return Order::with(['items.product', 'user'])->find($id);
    }

    public function create(array $data, array $items): Order
    {
        $order = Order::create($data);

        foreach ($items as $item) {
            $order->items()->create($item);
        }

        return $order->load(['items.product']);
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order->fresh(['items.product', 'user']);
    }
}
