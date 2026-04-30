<?php

namespace App\Http\Requests\Pool;

use Illuminate\Foundation\Http\FormRequest;

class PoolJoinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'join_code' => ['required', 'string', 'exists:pools,join_code'],
        ];
    }

    public function messages(): array
    {
        return [
            'join_code.required' => 'O código de entrada é obrigatório.',
            'join_code.string' => 'O código de entrada deve ser uma string.',
            'join_code.exists' => 'O código de entrada fornecido é inválido.',
        ];
    }
}
