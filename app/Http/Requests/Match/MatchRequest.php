<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Enums\MatchStage;
use App\Http\Enums\MatchStatus;

class MatchRequest extends FormRequest
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
            'code_home_team' => ['required', 'string', 'max:3'],
            'code_away_team' => ['required', 'string', 'max:3'],
            'home_score' => ['sometimes', 'nullable', 'integer', 'gte:0',  'min:0'],
            'away_score' => ['sometimes', 'nullable', 'integer', 'gte:0', 'min:0'],
            'kickoff_at' => ['sometimes', 'nullable', 'date_format:d/m/Y'],
            'stage' => [Rule::enum(MatchStage::class), 'required'],
            'status' => ['sometimes', 'nullable', Rule::enum(MatchStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'code_home_team.required' => 'O código do time da casa é obrigatório.',
            'code_home_team.string' => 'O código do time da casa deve ser uma string.',
            'code_home_team.max' => 'O código do time da casa deve ter no máximo :max caracteres.',
            'code_away_team.required' => 'O código do time visitante é obrigatório.',
            'code_away_team.string' => 'O código do time visitante deve ser uma string.',
            'code_away_team.max' => 'O código do time visitante deve ter no máximo :max caracteres.',
            'stage.required' => 'A fase da partida é obrigatória.',
            'stage.enum' => 'A fase da partida deve ser um dos seguintes valores: GROUP, ROUND_OF_16, QUARTER_FINALS, SEMI_FINALS, FINAL.',
        ];
    }
}
