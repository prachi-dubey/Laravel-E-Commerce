<?php

namespace App\Interfaces;

use App\Constants\Pagination;
use App\Models\ProductAuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductAuditLogRepositoryInterface
{
    public function paginate(int $perPage = Pagination::DEFAULT_PER_PAGE): LengthAwarePaginator;

    public function log(
        ?int $productId,
        ?int $userId,
        string $action,
        ?array $oldValues,
        ?array $newValues
    ): ProductAuditLog;
}
