<?php

namespace App\Interfaces;

use App\Models\ProductAuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductAuditLogRepositoryInterface
{
    public function paginate(?int $perPage = null): LengthAwarePaginator;

    public function log(
        ?int $productId,
        ?int $userId,
        string $action,
        ?array $oldValues,
        ?array $newValues
    ): ProductAuditLog;
}
