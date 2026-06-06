<?php

namespace App\Services\GuessServices;

use App\Http\Enums\GuessPoints;
use App\Http\Enums\MatchStatus;
use App\Models\Guess;
use App\Models\Matches;
use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\MatchRepositories\MatchRepository;
use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use App\Services\LeaderboardServices\LeaderboardWriteService;
use Illuminate\Support\Facades\DB;

class GuessScoringService
{
    public function __construct(
        private GuessRepository $guessRepository,
        private MatchRepository $matchRepository,
        private PoolMemberRepository $poolMemberRepository,
        private LeaderboardWriteService $leaderboardWriteService,
    ) {}

    private function scoreGuess(Guess $guess, Matches $match): int
    {
        if ($match->home_score === $guess->home_score && $match->away_score === $guess->away_score) {
            return GuessPoints::EXACT->value;
        }

        if (
            ($match->home_score > $match->away_score && $guess->home_score > $guess->away_score)
            || ($match->home_score < $match->away_score && $guess->home_score < $guess->away_score)
            || ($match->home_score === $match->away_score && $guess->home_score === $guess->away_score)
        ) {
            return GuessPoints::RESULT->value;
        }

        return GuessPoints::MISS->value;
    }

    public function scoreGuessesForMatch(int $matchId): void
    {
        $match = $this->matchRepository->findById($matchId);
        if (!$match) {
            throw new \Exception('Partida não encontrada.', 404);
        }

        if ($match->status !== MatchStatus::FINISHED) {
            throw new \Exception('A partida ainda não foi finalizada.', 400);
        }

        $guesses = $this->guessRepository->getByMatch($matchId);

        DB::transaction(function () use ($guesses, $match) {
            foreach ($guesses as $guess) {
                $points = $this->scoreGuess($guess, $match);
                $this->guessRepository->updateById($guess->id, ['points' => $points]);
            }

            $userIds = $guesses->pluck('user_id')->unique();

            foreach ($userIds as $userId) {
                $poolIds = $this->poolMemberRepository->getPoolIdsByUser($userId);
                foreach ($poolIds as $poolId) {
                    $this->leaderboardWriteService->syncUser($poolId, $userId);
                }
            }
        });
    }
}
