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

    public function paginate(?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('constants.pagination.default_per_page');
        $page = request()->integer('page', (int) config('constants.pagination.default_page'));
        $version = Cache::get(self::CACHE_VERSION_KEY, 1);

        return Cache::remember(
            "products.list.v{$version}.page.{$page}.per.{$perPage}",
            self::CACHE_TTL,
            fn () => Product::active()
                ->latest()
                ->paginate($perPage)
        );
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
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
