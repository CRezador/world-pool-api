<?php

namespace Database\Seeders;

use App\Repositories\LeaderboardRepositories\LeaderboardRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Popula o leaderboard do Bolão da Família (FAM001) com dados de teste.
 *
 * Pontuações calculadas para 7 partidas FINISHED (consistente com GuessesSeeder):
 *   user01 / user02 → 21 pts (7 cravadas)
 *   user03          →  7 pts (7 acertos)
 *   user04 / user05 →  5 pts (1 cravada + 2 acertos)
 *   alice           →  4 pts (4 acertos)
 *   user06 / user07 →  4 pts (1 cravada + 1 acerto)
 *   user08          →  4 pts (4 acertos)
 *   bob / user09 / user10 → 3 pts (3 acertos)
 *   carol           →  2 pts (2 acertos)
 */
class FamiliaLeaderboardSeeder extends Seeder
{
    private array $scores = [
        'user01@bolao.test' => ['points' => 21, 'exact_hits' => 7, 'result_hits' => 0, 'guesses_count' => 7],
        'user02@bolao.test' => ['points' => 21, 'exact_hits' => 7, 'result_hits' => 0, 'guesses_count' => 7],
        'user03@bolao.test' => ['points' =>  7, 'exact_hits' => 0, 'result_hits' => 7, 'guesses_count' => 7],
        'user04@bolao.test' => ['points' =>  5, 'exact_hits' => 1, 'result_hits' => 2, 'guesses_count' => 7],
        'user05@bolao.test' => ['points' =>  5, 'exact_hits' => 1, 'result_hits' => 2, 'guesses_count' => 7],
        'alice@bolao.test'  => ['points' =>  4, 'exact_hits' => 0, 'result_hits' => 4, 'guesses_count' => 7],
        'user06@bolao.test' => ['points' =>  4, 'exact_hits' => 1, 'result_hits' => 1, 'guesses_count' => 7],
        'user07@bolao.test' => ['points' =>  4, 'exact_hits' => 1, 'result_hits' => 1, 'guesses_count' => 7],
        'user08@bolao.test' => ['points' =>  4, 'exact_hits' => 0, 'result_hits' => 4, 'guesses_count' => 7],
        'bob@bolao.test'    => ['points' =>  3, 'exact_hits' => 0, 'result_hits' => 3, 'guesses_count' => 7],
        'user09@bolao.test' => ['points' =>  3, 'exact_hits' => 0, 'result_hits' => 3, 'guesses_count' => 7],
        'user10@bolao.test' => ['points' =>  3, 'exact_hits' => 0, 'result_hits' => 3, 'guesses_count' => 7],
        'carol@bolao.test'  => ['points' =>  2, 'exact_hits' => 0, 'result_hits' => 2, 'guesses_count' => 7],
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
            ];

            DB::table('leaderboard')->updateOrInsert(
                ['pool_id' => $poolId, 'user_id' => $member->user_id],
                array_merge($score, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        app(LeaderboardRepository::class)->updateRanks($poolId);

        $this->command->info("Leaderboard do Bolão da Família populado com {$members->count()} membros.");
    }
}
