<?php

namespace App\Services\GuessServices;

use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\MatchRepositories\MatchRepository;
use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use Illuminate\Database\Eloquent\Collection;

class GuessReadService
{
    public function __construct(
        private GuessRepository $guessRepository,
        private MatchRepository $matchRepository,
        private PoolMemberRepository $poolMemberRepository,
    ) {}

    public function getMyGuesses(int $poolId, int $userId): Collection
    {
        return $this->guessRepository->getByUserAndPool($userId, $poolId);
    }

    public function getMatchGuesses(int $matchId, int $poolId): Collection
    {
        $match = $this->matchRepository->findById($matchId);
        if (!$match) {
            throw new \Exception('Partida não encontrada.', 404);
        }

        return $this->guessRepository->getByMatchAndPool($matchId, $poolId);
    }

    public function getMemberGuesses(int $memberId, int $poolId): Collection
    {
        $member = $this->poolMemberRepository->getMemberById($poolId, $memberId);
        if (!$member) {
            throw new \Exception('Membro não encontrado neste bolão.', 404);
        }

        return $this->guessRepository->getByMemberAndPool($member->user_id, $poolId);
    }
}
