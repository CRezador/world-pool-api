<?php

namespace App\Http\Requests\Match;

use App\Http\Enums\MatchStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Enums\MatchStatus;
use Illuminate\Support\Str;

class MatchUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('status')) {
            $this->merge([
                'status' => Str::upper($this->input('status')),
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $fields = [
                'home_score',
                'away_score',
                'kickoff_at',
                'status'
            ];

            $hasField = collect($this->only($fields))
                ->filter(fn($value) => !is_null($value))
                ->isNotEmpty();

            if (!$hasField) {
                $validator->errors()->add(
                    'request',
                    'Pelo menos um campo precisa ser enviado.'
                );
            }
        });
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'home_score' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'away_score' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'kickoff_at' => ['sometimes', 'nullable', 'date_format:d/m/Y'],
            'status' => ['sometimes', 'nullable', Rule::enum(MatchStatus::class)]
        ];
    }

    public function messages(): array
    {
        return [
            'home_score.integer' => 'O placar do time da casa deve ser um número inteiro.',
            'home_score.min' => 'O placar do time da casa deve ser um número inteiro não negativo.',
            'away_score.integer' => 'O placar do time visitante deve ser um número inteiro.',
            'away_score.min' => 'O placar do time visitante deve ser um número inteiro não negativo.',
            'kickoff_at.date_format' => 'A data de início da partida deve estar no formato d/m/Y.',
            'status.enum' => 'A Status da partida deve ser um dos seguintes valores: SCHEDULED, IN_PROGRESS, FINISHED.'
        ];
    }
}
