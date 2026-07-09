<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'subtotal' => $this->when(
                $this->relationLoaded('product') && $this->product,
                fn () => bcmul((string) $this->product->price, (string) $this->quantity, 2)
            ),
        ];
    }
}
