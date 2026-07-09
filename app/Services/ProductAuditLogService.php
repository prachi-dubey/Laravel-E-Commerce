<?php

namespace App\Services;

use App\Interfaces\ProductAuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductAuditLogService
{
    public function __construct(
        private readonly ProductAuditLogRepositoryInterface $auditLogRepository,
    ) {}

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return $this->auditLogRepository->paginate($perPage);
    }
}
