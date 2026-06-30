<?php

namespace App\Http\Transformers\GuessTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class AdversaryGuessTransformer extends BaseTransformer
{
    public function transform(mixed $guess): array
    {
        $m = $guess->match;

        return [
            'id'             => $guess->id,
            'home_score'     => $guess->home_score,
            'away_score'     => $guess->away_score,
            'winner_team_id' => $guess->winner_team_id,
            'points'         => $guess->points,
            'user'       => [
                'id'       => $guess->user->id,
                'name'     => $guess->user->name,
                'initials' => $this->initials($guess->user->name),
            ],
            // Bolões que esse adversário compartilha comigo (anotado no service).
            'pools'      => $guess->shared_pools ?? [],
            'match'      => [
                'id'             => $m->id,
                'status'         => $m->status->name,
                'home_score'     => $m->home_score,
                'away_score'     => $m->away_score,
                'home_penalties' => $m->home_penalties,
                'away_penalties' => $m->away_penalties,
                'winner_team_id' => $m->winner_team_id,
                'home_team'  => ['id' => $m->home_team_id, 'code' => $m->homeTeam->code],
                'away_team'  => ['id' => $m->away_team_id, 'code' => $m->awayTeam->code],
            ],
        ];
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (empty($parts)) {
            return '?';
        }
        $first = mb_substr($parts[0], 0, 1);
        $last  = \count($parts) > 1 ? mb_substr($parts[\count($parts) - 1], 0, 1) : '';

        return mb_strtoupper($first . $last);
    }
}
