<?php

namespace App\Http\Requests\Pool;

use Illuminate\Foundation\Http\FormRequest;

class PoolUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_public' => ['sometimes', 'boolean'],
            'name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
