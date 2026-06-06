<?php

namespace App\Services\StandingsServices;

use App\Models\Standing;
use Illuminate\Support\Collection;

class StandingsReadService
{
    public function getStandings(): Collection
    {
        return Standing::with('team')
            ->orderBy('group')
            ->orderBy('position')
            ->get()
            ->groupBy('group')
            ->map(fn(Collection $rows, string $group) => [
                'group' => "Group {$group}",
                'table' => $rows->map(fn(Standing $s) => [
                    'position'       => $s->position,
                    'playedGames'    => $s->played,
                    'won'            => $s->won,
                    'draw'           => $s->draw,
                    'lost'           => $s->lost,
                    'goalsFor'       => $s->goals_for,
                    'goalsAgainst'   => $s->goals_against,
                    'goalDifference' => $s->goal_diff,
                    'points'         => $s->points,
                    'team'           => [
                        'tla'  => $s->team->code,
                        'name' => $s->team->name,
                        'crest' => null,
                    ],
                ])->values()->toArray(),
            ])
            ->values();
    }
}
