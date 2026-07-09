<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = CartItemResource::collection($this->whenLoaded('items'));
        $total = '0.00';

        if ($this->relationLoaded('items')) {
            foreach ($this->items as $item) {
                if ($item->product) {
                    $total = bcadd($total, bcmul((string) $item->product->price, (string) $item->quantity, 2), 2);
                }
            }
        }

        return [
            'id' => $this->id,
            'items' => $items,
            'total_amount' => $total,
        ];
    }
}
