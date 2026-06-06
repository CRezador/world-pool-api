<?php

namespace Database\Seeders;

use App\Repositories\LeaderboardRepositories\LeaderboardRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Popula o leaderboard do Bolão da Família (FAM001) com dados de teste.
 *
 * Pontuações calculadas para 7 partidas FINISHED (consistente com GuessesSeeder):
 *   user01 / user02 → 21 pts (7 cravadas)    → posições 1-2
 *   user03          →  7 pts (7 acertos)       → posição 3
 *   user04 / user05 →  5 pts (1 cravada + 2)  → posições 4-5
 *   user06 / user07 →  4 pts (1 cravada + 1)  → posições 6-7
 *   alice / user08  →  4 pts (4 acertos)       → posições 8-9
 *   bob/user09/10   →  3 pts (3 acertos)       → posições 10-12
 *   carol           →  2 pts (2 acertos)       → posição 13
 *
 * O campo `position` no insert representa a posição ANTERIOR (simulada),
 * pois updateRanks() a rotaciona para `previous_position` antes de recalcular.
 */
class FamiliaLeaderboardSeeder extends Seeder
{
    private array $scores = [
        //                                                              scores atuais          pos. anterior (simulada p/ trend)
        'user01@bolao.test' => ['points' => 21, 'exact_hits' => 7, 'result_hits' => 0, 'guesses_count' => 7, 'position' => 3],  // subiu ▲
        'user02@bolao.test' => ['points' => 21, 'exact_hits' => 7, 'result_hits' => 0, 'guesses_count' => 7, 'position' => 1],  // desceu ▼
        'user03@bolao.test' => ['points' =>  7, 'exact_hits' => 0, 'result_hits' => 7, 'guesses_count' => 7, 'position' => 2],  // desceu ▼
        'user04@bolao.test' => ['points' =>  5, 'exact_hits' => 1, 'result_hits' => 2, 'guesses_count' => 7, 'position' => 5],  // subiu ▲
        'user05@bolao.test' => ['points' =>  5, 'exact_hits' => 1, 'result_hits' => 2, 'guesses_count' => 7, 'position' => 4],  // desceu ▼
        'user06@bolao.test' => ['points' =>  4, 'exact_hits' => 1, 'result_hits' => 1, 'guesses_count' => 7, 'position' => 8],  // subiu ▲
        'user07@bolao.test' => ['points' =>  4, 'exact_hits' => 1, 'result_hits' => 1, 'guesses_count' => 7, 'position' => 7],  // igual ·
        'alice@bolao.test'  => ['points' =>  4, 'exact_hits' => 0, 'result_hits' => 4, 'guesses_count' => 7, 'position' => 6],  // desceu ▼
        'user08@bolao.test' => ['points' =>  4, 'exact_hits' => 0, 'result_hits' => 4, 'guesses_count' => 7, 'position' => 11], // subiu ▲
        'bob@bolao.test'    => ['points' =>  3, 'exact_hits' => 0, 'result_hits' => 3, 'guesses_count' => 7, 'position' => 9],  // desceu ▼
        'user09@bolao.test' => ['points' =>  3, 'exact_hits' => 0, 'result_hits' => 3, 'guesses_count' => 7, 'position' => 11], // igual ·
        'user10@bolao.test' => ['points' =>  3, 'exact_hits' => 0, 'result_hits' => 3, 'guesses_count' => 7, 'position' => 12], // igual ·
        'carol@bolao.test'  => ['points' =>  2, 'exact_hits' => 0, 'result_hits' => 2, 'guesses_count' => 7, 'position' => 13], // igual ·
    ];

    public function run(): void
    {
        $poolId = DB::table('pools')->where('join_code', 'FAM001')->value('id');

        if (!$poolId) {
            $this->command->warn('Pool FAM001 não encontrado. Rode PoolsSeeder primeiro.');
            return;
        }

        $members = DB::table('pool_members as pm')
            ->join('users as u', 'u.id', '=', 'pm.user_id')
            ->where('pm.pool_id', $poolId)
            ->where('pm.status', 'ACTIVE')
            ->select('u.id as user_id', 'u.email')
            ->get();

        $now = now();

        foreach ($members as $member) {
            $score = $this->scores[$member->email] ?? [
                'points'        => 0,
                'exact_hits'    => 0,
                'result_hits'   => 0,
                'guesses_count' => 0,
                'position'      => null,
            ];

            DB::table('leaderboard')->updateOrInsert(
                ['pool_id' => $poolId, 'user_id' => $member->user_id],
                array_merge($score, [
                    'previous_position' => null,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ])
            );
        }

        app(LeaderboardRepository::class)->updateRanks($poolId);

        $this->command->info("Leaderboard do Bolão da Família populado com {$members->count()} membros.");
    }
}
