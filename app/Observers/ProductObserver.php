<?php

namespace App\Observers;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductObserver
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    public function updated(Product $product): void
    {
        $this->productRepository->clearListCache();
    }

    public function deleted(Product $product): void
    {
        $this->productRepository->clearListCache();
    }
}
