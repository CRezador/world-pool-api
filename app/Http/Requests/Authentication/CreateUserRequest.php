<?php

declare(strict_types=1);

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['unique:users,email', 'required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.min' => 'A senha deve ter no mínimo :min caracteres.',
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'Email é obrigatório.',
            'email.email' => 'Email inválido.',
            'email.unique' => 'Email já está em uso.',
            'password.required' => 'Senha é obrigatória.',
        ];
    }
}
