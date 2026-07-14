<?php

namespace App\Services;

use App\Constants\Pagination;
use App\Interfaces\ProductAuditLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductAuditLogService
{
    public function __construct(
        private readonly ProductAuditLogRepositoryInterface $auditLogRepository,
    ) {}

    public function list(int $perPage = Pagination::DEFAULT_PER_PAGE): LengthAwarePaginator
    {
        return $this->auditLogRepository->paginate($perPage);
    }
}
