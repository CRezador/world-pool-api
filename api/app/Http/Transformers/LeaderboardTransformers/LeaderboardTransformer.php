<?php

namespace App\Http\Transformers\LeaderboardTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class LeaderboardTransformer extends BaseTransformer
{
    public function transform($leaderboard): array
    {
        return [
            'points' => $leaderboard->points,
            'exact_hits' => $leaderboard->exact_hits,
            'result_hits' => $leaderboard->result_hits,
            'guesses_count' => $leaderboard->guesses_count,
        ];
    }
}
