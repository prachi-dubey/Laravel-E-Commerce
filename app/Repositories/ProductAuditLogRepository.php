<?php

namespace App\Repositories;

use App\Interfaces\ProductAuditLogRepositoryInterface;
use App\Models\ProductAuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductAuditLogRepository implements ProductAuditLogRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return ProductAuditLog::query()
            ->with(['product', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function log(
        ?int $productId,
        ?int $userId,
        string $action,
        ?array $oldValues,
        ?array $newValues
    ): ProductAuditLog {
        return ProductAuditLog::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
