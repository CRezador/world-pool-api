<?php

namespace App\Http\Transformers\StandingsTransformers;

use App\Http\Transformers\BaseTransformers\BaseTransformer;
use App\Models\Team;
use Illuminate\Support\Collection;

class StandingsTransformer extends BaseTransformer
{
    private Collection $teams;

    public function __construct()
    {
        $this->teams = Team::all()->keyBy('code');
    }

    public function transform(mixed $standing): array
    {
        return [
            'group' => str_replace('Group ', '', $standing['group']),
            'table' => collect($standing['table'])->map(function ($row) {
                $team = $this->teams->get($row['team']['tla']);

                return [
                    'position'      => $row['position'],
                    'team'          => $team?->name ?? $row['team']['name'],
                    'crest'         => $team?->flag_code
                        ? "https://flagcdn.com/{$team->flag_code}.svg"
                        : $row['team']['crest'],
                    'played'        => $row['playedGames'],
                    'won'           => $row['won'],
                    'draw'          => $row['draw'],
                    'lost'          => $row['lost'],
                    'goals_for'     => $row['goalsFor'],
                    'goals_against' => $row['goalsAgainst'],
                    'goal_diff'     => $row['goalDifference'],
                    'points'        => $row['points'],
                ];
            })->values()->toArray(),
        ];
    }
}
