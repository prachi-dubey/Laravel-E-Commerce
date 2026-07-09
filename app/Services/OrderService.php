<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Exceptions\CustomException;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CartService $cartService,
    ) {}

    public function listForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->paginateForUser($userId, $perPage);
    }

    public function listAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->paginateAll($perPage);
    }

    public function show(int $id, User $user): Order
    {
        $order = $this->orderRepository->findById($id);

        if (! $order) {
            throw new CustomException(__('messages.resource_not_found'), Response::HTTP_NOT_FOUND);
        }

        if ($user->isCustomer() && $order->user_id !== $user->id) {
            throw new CustomException(__('messages.unauthorized_access'), Response::HTTP_FORBIDDEN);
        }

        return $order;
    }

    public function placeOrder(User $user): Order
    {
        $cart = $this->cartRepository->findForUser($user->id);

        if (! $cart || $cart->items->isEmpty()) {
            throw new CustomException(__('messages.cart_empty'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return DB::transaction(function () use ($user, $cart) {
            $total = 0;
            $orderItems = [];

            foreach ($cart->items as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);

                if (! $product || ! $product->is_active) {
                    throw new CustomException(
                        __('messages.product_unavailable', ['name' => $product?->name ?? 'Unknown']),
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                if ($product->stock < $item->quantity) {
                    throw new CustomException(
                        __('messages.insufficient_stock_for_product', ['name' => $product->name]),
                        Response::HTTP_UNPROCESSABLE_ENTITY
                    );
                }

                $subtotal = bcmul((string) $product->price, (string) $item->quantity, 2);
                $total = bcadd((string) $total, $subtotal, 2);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                $product->decrement('stock', $item->quantity);
            }

            $order = $this->orderRepository->create([
                'user_id' => $user->id,
                'status' => OrderStatus::Pending->value,
                'total_amount' => $total,
            ], $orderItems);

            $this->cartService->clearCart($user->id);

            OrderPlaced::dispatch($order);

            return $order;
        });
    }

    public function updateStatus(Order $order, string $newStatus): Order
    {
        $currentStatus = $order->status;
        $allowed = $currentStatus->allowedTransitions();

        if (! in_array($newStatus, $allowed, true)) {
            throw new CustomException(
                __('messages.invalid_status_transition', [
                    'from' => $currentStatus->value,
                    'to' => $newStatus,
                ]),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($newStatus === OrderStatus::Cancelled->value) {
            $this->restoreStock($order);
        }

        return $this->orderRepository->updateStatus($order, $newStatus);
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            Product::query()
                ->where('id', $item->product_id)
                ->increment('stock', $item->quantity);
        }
    }
}
