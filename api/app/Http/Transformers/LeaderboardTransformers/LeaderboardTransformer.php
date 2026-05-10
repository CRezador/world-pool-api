<?php

namespace App\Http\Transformers\LeaderboardTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
class LeaderboardTransformer extends BaseTransformer
{
    public function transform(mixed $leaderboard): array
    {
        return [
            'rank'          => $leaderboard->rank,
            'user'          => [
                'id'   => $leaderboard->user->id,
                'name' => $leaderboard->user->name,
            ],
            'points'        => $leaderboard->points,
            'exact_hits'    => $leaderboard->exact_hits,
            'result_hits'   => $leaderboard->result_hits,
            'guesses_count' => $leaderboard->guesses_count,
            'updated_at'    => $leaderboard->updated_at?->toDateTimeString(),
        ];
    }
}
