<?php

namespace App\Http\Requests\Api;

class UpdateCartItemRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
