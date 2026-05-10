<?php

namespace App\Http\Requests\Guess;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuessRequest extends FormRequest
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
            "match_id" => [
                "required",
                "integer",
                "exists:matches,id",
                Rule::unique('guesses', 'match_id')
                    ->where('pool_id', $this->route('poolId'))
                    ->where('user_id', $this->user()->id),
            ],
            "home_score" => ["required", "integer", "min:0"],
            "away_score" => ["required", "integer", "min:0"],
        ];
    }

    public function messages(): array
    {
        return [
            "match_id.required" => "O campo match_id é obrigatório.",
            "match_id.integer" => "O campo match_id deve ser um inteiro.",
            "match_id.exists" => "O match_id especificado não existe.",
            "match_id.unique" => "Você já possui um palpite para esta partida neste bolão.",
            "home_score.required" => "O campo home_score é obrigatório.",
            "home_score.integer" => "O campo home_score deve ser um inteiro.",
            "home_score.min" => "O campo home_score deve ser pelo menos 0.",
            "away_score.required" => "O campo away_score é obrigatório.",
            "away_score.integer" => "O campo away_score deve ser um inteiro.",
            "away_score.min" => "O campo away_score deve ser pelo menos 0.",
        ];
    }
}
