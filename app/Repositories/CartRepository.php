<?php

namespace App\Repositories;

use App\Interfaces\CartRepositoryInterface;
use App\Models\Cart;

class CartRepository implements CartRepositoryInterface
{
    public function getOrCreateForUser(int $userId): Cart
    {
        return Cart::firstOrCreate(['user_id' => $userId]);
    }

    public function findForUser(int $userId): ?Cart
    {
        return Cart::with(['items.product'])
            ->where('user_id', $userId)
            ->first();
    }
}
