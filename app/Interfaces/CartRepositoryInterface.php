<?php

namespace App\Interfaces;

use App\Models\Cart;

interface CartRepositoryInterface
{
    public function getOrCreateForUser(int $userId): Cart;

    public function findForUser(int $userId): ?Cart;
}
