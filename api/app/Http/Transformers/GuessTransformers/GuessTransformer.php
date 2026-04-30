<?php

namespace App\Http\Transformers\GuessTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class GuessTransformer extends BaseTransformer
{
    public function transform($guess): array
    {
        return [
            'id' => $guess->id,
            'home_score' => $guess->home_score,
            'away_score' => $guess->away_score,
            'points' => $guess->points,
        ];
    }
}
