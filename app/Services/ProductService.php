<?php

namespace App\Services;

use App\Constants\Pagination;
use App\Exceptions\CustomException;
use App\Interfaces\ProductAuditLogRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductAuditLogRepositoryInterface $auditLogRepository,
    ) {}

    public function list(int $perPage = Pagination::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage);
    }

    public function listAll(int $perPage = Pagination::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return Product::query()->withTrashed()->latest()->paginate($perPage);
    }

    public function show(int $id): Product
    {
        $product = $this->productRepository->findById($id);

        if (! $product || (! $product->is_active && ! request()->user()?->isAdmin())) {
            throw new CustomException(__('messages.resource_not_found'), Response::HTTP_NOT_FOUND);
        }

        return $product;
    }

    public function create(array $data, ?UploadedFile $image, User $user): Product
    {
        if ($image) {
            $data['image_path'] = $image->store('products', 'public');
        }

        $product = $this->productRepository->create($data);

        $this->auditLogRepository->log(
            $product->id,
            $user->id,
            'created',
            null,
            $product->toArray()
        );

        return $product;
    }

    public function update(Product $product, array $data, ?UploadedFile $image, User $user): Product
    {
        $oldValues = $product->toArray();

        if ($image) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $image->store('products', 'public');
        }

        $updated = $this->productRepository->update($product, $data);

        $this->auditLogRepository->log(
            $product->id,
            $user->id,
            'updated',
            $oldValues,
            $updated->toArray()
        );

        return $updated;
    }

    public function delete(Product $product, User $user): void
    {
        $oldValues = $product->toArray();

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $this->productRepository->delete($product);

        $this->auditLogRepository->log(
            $product->id,
            $user->id,
            'deleted',
            $oldValues,
            null
        );
    }
}
