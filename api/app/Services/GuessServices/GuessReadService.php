<?php

namespace App\Services\GuessServices;

use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\MatchRepositories\MatchRepository;
use Illuminate\Database\Eloquent\Collection;

class GuessReadService
{
    public function __construct(
        private GuessRepository $guessRepository,
        private MatchRepository $matchRepository
    ) {}

    public function getMyGuesses(int $poolId, int $userId): Collection
    {
        return $this->guessRepository->getByUserAndPool($userId, $poolId);
    }

    public function getMatchGuesses(int $matchId, int $poolId): Collection
    {
        $this->matchRepository->assertScheduled($matchId);
        return $this->guessRepository->getByMatchAndPool($matchId, $poolId);
    }

    public function getMemberGuesses(int $memberId, int $poolId): Collection
    {
        return $this->guessRepository->getByMemberAndPool($memberId, $poolId);
    }
}
