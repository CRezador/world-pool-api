<?php

namespace App\Http\Requests\Authentication;

use App\Http\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', new Enum(UserRole::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'O papel é obrigatório.',
            'role.enum' => 'Papel inválido. Os valores aceitos são: USER, ADMIN.',
        ];
    }
}
