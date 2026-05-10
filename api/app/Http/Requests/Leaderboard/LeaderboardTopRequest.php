<?php

namespace App\Http\Requests\Leaderboard;

use Illuminate\Foundation\Http\FormRequest;

class LeaderboardTopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => ['sometimes', 'integer', 'min:1', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'limit.integer' => 'O campo limit deve ser um número inteiro.',
            'limit.min'     => 'O limite mínimo é 1.',
            'limit.max'     => 'O limite máximo é 10.',
        ];
    }

    public function getLimit(): int
    {
        return (int) $this->input('limit', 3);
    }
}
