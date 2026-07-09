<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    private const CACHE_VERSION_KEY = 'products.cache_version';

    private const CACHE_TTL = 300;

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        $page = request()->integer('page', 1);
        $version = Cache::get(self::CACHE_VERSION_KEY, 1);

        return Cache::remember(
            "products.list.v{$version}.page.{$page}.per.{$perPage}",
            self::CACHE_TTL,
            fn () => Product::query()
                ->where('is_active', true)
                ->latest()
                ->paginate($perPage)
        );
    }

    public function findById(int $id): ?Product
    {
        return Product::query()->find($id);
    }

    public function create(array $data): Product
    {
        $product = Product::create($data);
        $this->clearListCache();

        return $product;
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        $this->clearListCache();

        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        $deleted = (bool) $product->delete();
        $this->clearListCache();

        return $deleted;
    }

    public function clearListCache(): void
    {
        Cache::increment(self::CACHE_VERSION_KEY);
    }
}
