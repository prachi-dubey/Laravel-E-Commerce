<?php

namespace App\Http\Requests\Api;

use App\Enums\OrderStatus;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(OrderStatus::values())],
        ];
    }
}
