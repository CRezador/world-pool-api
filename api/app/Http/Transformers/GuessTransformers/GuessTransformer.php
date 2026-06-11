<?php

namespace App\Http\Transformers\GuessTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;

class GuessTransformer extends BaseTransformer
{
    public function transform(mixed $guess): array
    {
        $data = [
            'id'         => $guess->id,
            'match_id'   => $guess->match_id,
            'home_score' => $guess->home_score,
            'away_score' => $guess->away_score,
            'points'     => $guess->points,
        ];

        if ($guess->relationLoaded('match') && $guess->match) {
            $m = $guess->match;
            $data['match'] = [
                'id'         => $m->id,
                'stage'      => $m->stage->name,
                'group'      => $m->stage->name === 'GROUP_STAGE' ? $m->group?->name : null,
                'status'     => $m->status->name,
                'kickoff_at' => $m->kickoff_at?->copy()->setTimezone(config('app.display_timezone'))->format('d/m/Y H:i'),
                'home_score' => $m->home_score,
                'away_score' => $m->away_score,
                'home_team'  => [
                    'code'     => $m->homeTeam->code,
                    'flag_url' => $m->homeTeam->flag_code
                        ? "https://flagcdn.com/{$m->homeTeam->flag_code}.svg"
                        : null,
                ],
                'away_team'  => [
                    'code'     => $m->awayTeam->code,
                    'flag_url' => $m->awayTeam->flag_code
                        ? "https://flagcdn.com/{$m->awayTeam->flag_code}.svg"
                        : null,
                ],
            ];
        }

        return $data;
    }
}
