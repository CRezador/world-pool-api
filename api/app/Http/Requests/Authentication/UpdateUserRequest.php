<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'email' => ['sometimes', 'string', 'email', 'max:255', "unique:users,email,{$userId}"],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Nome deve ser uma string.',
            'email.email' => 'Email inválido.',
            'email.unique' => 'Email já está em uso.',
        ];
    }
}
