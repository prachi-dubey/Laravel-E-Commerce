<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', (int) config('constants.pagination.default_per_page'));
        $user = $request->user();

        $orders = $user->isAdmin()
            ? $this->orderService->listAll($perPage)
            : $this->orderService->listForUser($user->id, $perPage);

        return $this->successResponse(__('messages.success'), [
            'orders' => OrderResource::collection($orders),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->show($id, $request->user());
        } catch (CustomException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        return $this->successResponse(__('messages.success'), new OrderResource($order));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $order = $this->orderService->placeOrder($request->user());
        } catch (CustomException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        return $this->successResponse(
            __('messages.order_placed'),
            new OrderResource($order),
            Response::HTTP_CREATED
        );
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->show($id, $request->user());
            $updated = $this->orderService->updateStatus($order, $request->string('status')->value());
        } catch (CustomException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        return $this->successResponse(__('messages.order_status_updated'), new OrderResource($updated));
    }
}
