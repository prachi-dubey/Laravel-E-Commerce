<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\CustomException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $products = $this->productService->list($perPage);

        return $this->successResponse(__('messages.success'), [
            'products' => ProductResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function adminIndex(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 15);
        $products = $this->productService->listAll($perPage);

        return $this->successResponse(__('messages.success'), [
            'products' => ProductResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->show($id);
        } catch (CustomException $e) {
            return $this->errorResponse($e->getMessage(), $e->getStatusCode());
        }

        return $this->successResponse(__('messages.success'), new ProductResource($product));
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create(
            $request->safe()->except('image'),
            $request->file('image'),
            $request->user()
        );

        return $this->successResponse(
            __('messages.product_created'),
            new ProductResource($product),
            Response::HTTP_CREATED
        );
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updated = $this->productService->update(
            $product,
            $request->safe()->except('image'),
            $request->file('image'),
            $request->user()
        );

        return $this->successResponse(__('messages.product_updated'), new ProductResource($updated));
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->productService->delete($product, $request->user());

        return $this->successResponse(__('messages.product_deleted'));
    }
}
