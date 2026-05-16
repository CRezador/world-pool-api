<?php

namespace Database\Seeders;

use App\Http\Enums\GuessPoints;
use App\Http\Enums\MatchStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuessesSeeder extends Seeder
{
    // Partidas finalizadas e seus resultados para referência:
    // 1. BRA 2-1 MEX  → vitória mandante
    // 2. ARG 1-1 FRA  → empate
    // 3. ESP 3-0 POR  → vitória mandante
    // 4. GHA 0-2 BEL  → vitória visitante
    // 5. JPN 1-2 COL  → vitória visitante
    // 6. KOR 0-0 AUS  → empate (único placar 0-0 → vale para quem chuta fixo 0-0)
    // 7. URU 2-0 CAN  → vitória mandante
    //
    // Pontos esperados por estratégia (7 partidas):
    //   user01 / user02 → offset(0,0) = exatos em tudo    → 21 pts
    //   user03          → offset(1,1) = resultado sempre   →  7 pts
    //   user04          → fixo(2,1) = 1 exato + 2 resultado→  5 pts
    //   user05          → fixo(3,0) = 1 exato + 2 resultado→  5 pts
    //   alice           → offset(0,1) = 4 resultado        →  4 pts
    //   user06          → fixo(0,2) = 1 exato + 1 resultado→  4 pts
    //   user07          → fixo(1,1) = 1 exato + 1 resultado→  4 pts
    //   user08          → offset(1,0) = 4 resultado        →  4 pts
    //   bob             → offset(0,2) = 3 resultado        →  3 pts
    //   user09          → offset(-1,1) = 3 resultado       →  3 pts
    //   user10          → offset(2,0) = 3 resultado        →  3 pts
    //   carol           → fixo(0,1) = 2 resultado          →  2 pts

    private array $strategies = [
        'user01@bolao.test' => ['type' => 'offset', 'dh' =>  0, 'da' =>  0],
        'user02@bolao.test' => ['type' => 'offset', 'dh' =>  0, 'da' =>  0],
        'user03@bolao.test' => ['type' => 'offset', 'dh' =>  1, 'da' =>  1],
        'user04@bolao.test' => ['type' => 'fixed',  'h'  =>  2, 'a'  =>  1],
        'user05@bolao.test' => ['type' => 'fixed',  'h'  =>  3, 'a'  =>  0],
        'user06@bolao.test' => ['type' => 'fixed',  'h'  =>  0, 'a'  =>  2],
        'user07@bolao.test' => ['type' => 'fixed',  'h'  =>  1, 'a'  =>  1],
        'user08@bolao.test' => ['type' => 'offset', 'dh' =>  1, 'da' =>  0],
        'user09@bolao.test' => ['type' => 'offset', 'dh' => -1, 'da' =>  1],
        'user10@bolao.test' => ['type' => 'offset', 'dh' =>  2, 'da' =>  0],
        'alice@bolao.test'  => ['type' => 'offset', 'dh' =>  0, 'da' =>  1],
        'bob@bolao.test'    => ['type' => 'offset', 'dh' =>  0, 'da' =>  2],
        'carol@bolao.test'  => ['type' => 'fixed',  'h'  =>  0, 'a'  =>  1],
    ];

    public function run(): void
    {
        $finishedMatches = DB::table('matches')
            ->where('status', MatchStatus::FINISHED->value)
            ->get(['id', 'home_score', 'away_score']);

        $pools = DB::table('pools')->get(['id']);

        $now     = now();
        $guesses = [];

        foreach ($pools as $pool) {
            $members = DB::table('pool_members as pm')
                ->join('users as u', 'u.id', '=', 'pm.user_id')
                ->where('pm.pool_id', $pool->id)
                ->where('pm.status', 'ACTIVE')
                ->select('u.id as user_id', 'u.email')
                ->get();

            foreach ($members as $member) {
                $strategy = $this->strategies[$member->email] ?? ['type' => 'offset', 'dh' => 0, 'da' => 0];

                foreach ($finishedMatches as $match) {
                    [$guessHome, $guessAway] = $this->applyStrategy($strategy, $match->home_score, $match->away_score);
                    $points = $this->computePoints($match->home_score, $match->away_score, $guessHome, $guessAway);

                    $guesses[] = [
                        'pool_id'    => $pool->id,
                        'user_id'    => $member->user_id,
                        'match_id'   => $match->id,
                        'home_score' => $guessHome,
                        'away_score' => $guessAway,
                        'points'     => $points,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        DB::table('guesses')->insert($guesses);
    }

    private function applyStrategy(array $strategy, int $realHome, int $realAway): array
    {
        if ($strategy['type'] === 'fixed') {
            return [$strategy['h'], $strategy['a']];
        }

        return [
            max(0, $realHome + $strategy['dh']),
            max(0, $realAway + $strategy['da']),
        ];
    }

    private function computePoints(int $realHome, int $realAway, int $guessHome, int $guessAway): int
    {
        if ($guessHome === $realHome && $guessAway === $realAway) {
            return GuessPoints::EXACT->value;
        }

        if (($guessHome <=> $guessAway) === ($realHome <=> $realAway)) {
            return GuessPoints::RESULT->value;
        }

        return GuessPoints::MISS->value;
    }
}
