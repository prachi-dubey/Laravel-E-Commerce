<?php

namespace App\Http\Requests\Api;

class ListProductsRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'min_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'nullable', 'numeric', 'min:0', 'gte:min_price'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        $filters = [];

        if ($this->filled('name')) {
            $filters['name'] = $this->string('name')->value();
        }

        if ($this->filled('min_price')) {
            $filters['min_price'] = $this->float('min_price');
        }

        if ($this->filled('max_price')) {
            $filters['max_price'] = $this->float('max_price');
        }

        return $filters;
    }
}
