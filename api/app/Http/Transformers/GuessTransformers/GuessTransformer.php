<?php

namespace App\Http\Transformers\GuessTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
use App\Models\Guess;

class GuessTransformer extends BaseTransformer
{
    public function transform(Guess $guess): array
    {
        return [
            'id' => $guess->id,
            'user_id' => $guess->user_id,
            'pool_id' => $guess->pool_id,
            'match_id' => $guess->match_id,
            'home_score' => $guess->home_score,
            'away_score' => $guess->away_score,
            'points' => $guess->points,
        ];
    }
}
