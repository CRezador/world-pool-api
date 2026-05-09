<?php

namespace App\Http\Requests\PoolMember;

use App\Http\Enums\PoolUserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PoolMemberUpdateRequest extends FormRequest
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
            'role' => ['required', Rule::in([PoolUserRole::ADMIN->value, PoolUserRole::MEMBER->value])],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'O campo role é obrigatório.',
            'role.in' => 'O campo role deve ser ADMIN ou MEMBER.',
        ];
    }
}
