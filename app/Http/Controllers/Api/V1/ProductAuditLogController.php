<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductAuditLogResource;
use App\Services\ProductAuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductAuditLogController extends Controller
{
    public function __construct(
        private readonly ProductAuditLogService $auditLogService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', (int) config('constants.pagination.default_per_page'));
        $logs = $this->auditLogService->list($perPage);

        return $this->successResponse(__('messages.success'), [
            'audit_logs' => ProductAuditLogResource::collection($logs),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'last_page' => $logs->lastPage(),
            ],
        ]);
    }
}
