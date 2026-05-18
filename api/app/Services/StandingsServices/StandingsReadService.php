<?php

namespace App\Services\StandingsServices;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class StandingsReadService
{
    public function getStandings(): Collection
    {
        $response = Http::withHeader('X-Auth-Token', config('services.football_data.token'))
            ->get('https://api.football-data.org/v4/competitions/WC/standings');

        if (!$response->successful()) {
            throw new \Exception('Erro ao buscar classificação externa.', 502);
        }

        return collect($response->json('standings'))
            ->filter(fn($s) => $s['type'] === 'TOTAL')
            ->values();
    }
}
