<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Define os primeiros 7 jogos como FINISHED com placares fixos.
 * Esses placares são a referência usada pelo GuessesSeeder para calcular pontos.
 *
 * match 1 → 2-1 (mandante)
 * match 2 → 1-1 (empate)
 * match 3 → 3-0 (mandante)
 * match 4 → 0-2 (visitante)
 * match 5 → 1-2 (visitante)
 * match 6 → 0-0 (empate)
 * match 7 → 2-0 (mandante)
 */
class FinishedMatchesSeeder extends Seeder
{
    private array $results = [
        1 => [2, 1],
        2 => [1, 1],
        3 => [3, 0],
        4 => [0, 2],
        5 => [1, 2],
        6 => [0, 0],
        7 => [2, 0],
    ];

    public function run(): void
    {
        foreach ($this->results as $matchId => [$home, $away]) {
            DB::table('matches')->where('id', $matchId)->update([
                'home_score' => $home,
                'away_score' => $away,
                'status'     => 'FINISHED',
            ]);
        }

        $this->command->info('7 partidas marcadas como FINISHED.');
    }
}
