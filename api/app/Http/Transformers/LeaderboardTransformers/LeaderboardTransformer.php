<?php

namespace App\Http\Transformers\LeaderboardTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class LeaderboardTransformer extends BaseTransformer
{
    public function transform(mixed $leaderboard): array
    {
        $data = [
            'rank'          => $leaderboard->position,
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

        if (isset($leaderboard->last_results)) {
            $data['last_results'] = $leaderboard->last_results;
        }

        return $data;
    }
}
