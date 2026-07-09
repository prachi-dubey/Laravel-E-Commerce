<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Interfaces\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
    ) {}

    public function getCart(int $userId): Cart
    {
        $cart = $this->cartRepository->findForUser($userId);

        if (! $cart) {
            return $this->cartRepository->getOrCreateForUser($userId)->load(['items.product']);
        }

        return $cart;
    }

    public function addItem(int $userId, int $productId, int $quantity): Cart
    {
        $product = Product::query()->where('is_active', true)->find($productId);

        if (! $product) {
            throw new CustomException(__('messages.resource_not_found'), Response::HTTP_NOT_FOUND);
        }

        if ($product->stock < $quantity) {
            throw new CustomException(__('messages.insufficient_stock'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $cart = $this->cartRepository->getOrCreateForUser($userId);

        $item = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($item) {
            $newQuantity = $item->quantity + $quantity;

            if ($product->stock < $newQuantity) {
                throw new CustomException(__('messages.insufficient_stock'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $item->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        return $cart->load(['items.product']);
    }

    public function updateItem(int $userId, int $itemId, int $quantity): Cart
    {
        $cart = $this->cartRepository->getOrCreateForUser($userId);

        $item = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('id', $itemId)
            ->first();

        if (! $item) {
            throw new CustomException(__('messages.resource_not_found'), Response::HTTP_NOT_FOUND);
        }

        $product = Product::query()->find($item->product_id);

        if (! $product || $product->stock < $quantity) {
            throw new CustomException(__('messages.insufficient_stock'), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $item->update(['quantity' => $quantity]);

        return $cart->load(['items.product']);
    }

    public function removeItem(int $userId, int $itemId): Cart
    {
        $cart = $this->cartRepository->getOrCreateForUser($userId);

        $item = CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('id', $itemId)
            ->first();

        if (! $item) {
            throw new CustomException(__('messages.resource_not_found'), Response::HTTP_NOT_FOUND);
        }

        $item->delete();

        return $cart->load(['items.product']);
    }

    public function clearCart(int $userId): void
    {
        $cart = $this->cartRepository->findForUser($userId);

        if ($cart) {
            $cart->items()->delete();
        }
    }
}
