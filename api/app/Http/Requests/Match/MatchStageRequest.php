<?php

namespace App\Http\Requests\Match;

use App\Http\Enums\MatchStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MatchStageRequest extends FormRequest
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
            'stage' => [Rule::enum(MatchStage::class), 'required'],
        ];
    }

    public function messages(): array
    {
        return [
            'stage.required' => 'Informe o stage da partida',
            'stage.enum' => 'A fase da partida deve ser um dos seguintes valores: GROUP_STAGE, ROUND_OF_16, QUARTER_FINALS, SEMI_FINALS, THIRD_PLACE, FINAL.',
        ];
    }
}
