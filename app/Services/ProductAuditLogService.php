<?php

namespace App\Services;

use App\Interfaces\ProductAuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductAuditLogService
{
    public function __construct(
        private readonly ProductAuditLogRepositoryInterface $auditLogRepository,
    ) {}

    public function list(?int $perPage = null): LengthAwarePaginator
    {
        $perPage ??= (int) config('constants.pagination.default_per_page');

        return $this->auditLogRepository->paginate($perPage);
    }
}
