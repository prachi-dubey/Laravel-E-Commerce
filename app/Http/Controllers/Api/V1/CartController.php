<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddCartItemRequest;
use App\Http\Requests\Api\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $cart = $this->cartService->getCart($request->user()->id);

        return $this->successResponse(__('messages.success'), new CartResource($cart));
    }

    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->addItem(
                $request->user()->id,
                $request->integer('product_id'),
                $request->integer('quantity')
            );
        } catch (CustomException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        return $this->successResponse(__('messages.cart_item_added'), new CartResource($cart));
    }

    public function updateItem(UpdateCartItemRequest $request, int $itemId): JsonResponse
    {
        try {
            $cart = $this->cartService->updateItem(
                $request->user()->id,
                $itemId,
                $request->integer('quantity')
            );
        } catch (CustomException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        return $this->successResponse(__('messages.cart_item_updated'), new CartResource($cart));
    }

    public function removeItem(Request $request, int $itemId): JsonResponse
    {
        try {
            $cart = $this->cartService->removeItem($request->user()->id, $itemId);
        } catch (CustomException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        return $this->successResponse(__('messages.cart_item_removed'), new CartResource($cart));
    }
}
