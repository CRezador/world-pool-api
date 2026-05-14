<?php

namespace App\Http\Requests\Match;

use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {

            $fields = [
                'home_score',
                'away_score',
                'kickoff_at',
                'status',
                'stage',
            ];

            $hasField = collect($this->only($fields))
                ->filter(fn($value) => null !== $value)
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
            'kickoff_at' => ['sometimes', 'nullable', 'date_format:d/m/Y H:i'],
            'status'     => ['sometimes', 'nullable', Rule::enum(MatchStatus::class)],
            'stage'      => ['sometimes', 'nullable', Rule::enum(MatchStage::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'home_score.integer' => 'O placar do time da casa deve ser um número inteiro.',
            'home_score.min' => 'O placar do time da casa deve ser um número inteiro não negativo.',
            'away_score.integer' => 'O placar do time visitante deve ser um número inteiro.',
            'away_score.min' => 'O placar do time visitante deve ser um número inteiro não negativo.',
            'kickoff_at.date_format' => 'A data de início da partida deve estar no formato d/m/Y H:i.',
            'status.enum' => 'A Status da partida deve ser um dos seguintes valores: SCHEDULED, IN_PROGRESS, FINISHED.',
            'stage.enum' => 'A fase da partida deve ser um dos seguintes valores: GROUP_STAGE, ROUND_OF_16, QUARTER_FINALS, SEMI_FINALS, THIRD_PLACE, FINAL.',
        ];
    }
}
