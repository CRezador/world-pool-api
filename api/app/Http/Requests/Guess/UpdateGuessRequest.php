<?php

namespace App\Http\Requests\Guess;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGuessRequest extends FormRequest
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
            "home_score" => ["required", "integer", "min:0"],
            "away_score" => ["required", "integer", "min:0"],
        ];
    }

    public function messages(): array
    {
        return [
            "home_score.required" => "O campo home_score é obrigatório.",
            "home_score.integer" => "O campo home_score deve ser um inteiro.",
            "home_score.min" => "O campo home_score deve ser pelo menos 0.",
            "away_score.required" => "O campo away_score é obrigatório.",
            "away_score.integer" => "O campo away_score deve ser um inteiro.",
            "away_score.min" => "O campo away_score deve ser pelo menos 0.",
        ];
    }
}
