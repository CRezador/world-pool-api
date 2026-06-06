<?php

namespace App\Services\StandingsServices;

use App\Models\Standing;
use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class StandingsWriteService
{
    public function refresh(): void
    {
        $response = Http::withHeader('X-Auth-Token', config('services.football_data.token'))
            ->get('https://api.football-data.org/v4/competitions/WC/standings');

        if (!$response->successful()) {
            throw new \Exception('Erro ao buscar classificação externa.', 502);
        }

        $teams = Team::all()->keyBy('code');

        collect($response->json('standings'))
            ->filter(fn($s) => $s['type'] === 'TOTAL')
            ->each(function ($standing) use ($teams) {
                $group = str_replace('Group ', '', $standing['group']);

                collect($standing['table'])->each(function ($row) use ($group, $teams) {
                    $team = $teams->get($row['team']['tla']);

                    if (!$team) {
                        return;
                    }

                    Standing::updateOrCreate(
                        ['group' => $group, 'team_id' => $team->id],
                        [
                            'position'      => $row['position'],
                            'played'        => $row['playedGames'],
                            'won'           => $row['won'],
                            'draw'          => $row['draw'],
                            'lost'          => $row['lost'],
                            'goals_for'     => $row['goalsFor'],
                            'goals_against' => $row['goalsAgainst'],
                            'goal_diff'     => $row['goalDifference'],
                            'points'        => $row['points'],
                        ]
                    );
                });
            });
    }
}
