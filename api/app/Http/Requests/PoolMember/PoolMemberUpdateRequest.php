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
            'role' => [Rule::enum(PoolUserRole::class), 'required'],
            'user_id' => 'required|exists:pool_members,user_id',
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'O campo role é obrigatório.',
            'role.enum' => 'O campo role deve ser ADMIN ou MEMBER.',
            'user_id.required' => 'O campo user_id é obrigatório.',
            'user_id.exists' => 'O user_id fornecido não existe.',
        ];
    }
}
