<?php

namespace Database\Seeders;

use App\Http\Enums\GuessPoints;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeaderboardSeeder extends Seeder
{
    public function run(): void
    {
        $pools = DB::table('pools')->get(['id']);

        foreach ($pools as $pool) {
            $members = DB::table('pool_members')
                ->where('pool_id', $pool->id)
                ->where('status', 'ACTIVE')
                ->pluck('user_id');

            foreach ($members as $userId) {
                $guesses = DB::table('guesses')
                    ->where('pool_id', $pool->id)
                    ->where('user_id', $userId)
                    ->get(['points']);

                $total        = $guesses->sum('points');
                $exactHits    = $guesses->where('points', GuessPoints::EXACT->value)->count();
                $resultHits   = $guesses->where('points', GuessPoints::RESULT->value)->count();
                $guessesCount = $guesses->count();

                DB::table('leaderboard')->updateOrInsert(
                    ['pool_id' => $pool->id, 'user_id' => $userId],
                    [
                        'points'        => $total,
                        'exact_hits'    => $exactHits,
                        'result_hits'   => $resultHits,
                        'guesses_count' => $guessesCount,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]
                );
            }
        }
    }
}
