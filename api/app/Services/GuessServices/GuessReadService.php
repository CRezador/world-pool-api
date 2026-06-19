<?php

namespace App\Services\GuessServices;

use App\Http\Enums\MatchStatus;
use App\Models\Guess;
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

    public function getMyGuesses(int $userId): Collection
    {
        return $this->guessRepository->getByUser($userId);
    }

    public function getGuessForMatch(int $userId, int $matchId): ?Guess
    {
        return $this->guessRepository->getByUserAndMatch($userId, $matchId);
    }

    public function getMatchGuesses(int $matchId, int $poolId): Collection
    {
        $match = $this->matchRepository->findById($matchId);
        if (!$match) {
            throw new \Exception('Partida não encontrada.', 404);
        }

        return $this->guessRepository->getByMatchInPool($matchId, $poolId);
    }

    /**
     * Palpites dos adversários para uma partida: membros de qualquer bolão que o
     * usuário participa (exceto ele mesmo). Cada palpite é anotado com os bolões
     * compartilhados, permitindo o filtro por bolão no front.
     *
     * Privacidade: só revela quando a partida começou (IN_PROGRESS/FINISHED).
     */
    public function getAdversaryGuessesForMatch(int $userId, int $matchId): Collection
    {
        $match = $this->matchRepository->findById($matchId);
        if (!$match) {
            throw new \Exception('Partida não encontrada.', 404);
        }

        if ($match->status === MatchStatus::SCHEDULED) {
            throw new \Exception('Palpites liberados quando a partida começar.', 403);
        }

        $poolIds = $this->poolMemberRepository->getPoolIdsByUser($userId);
        if (empty($poolIds)) {
            return new Collection();
        }

        // Mapa user_id => [ {id, name} dos bolões compartilhados ], excluindo eu.
        $sharedPools = [];
        foreach ($this->poolMemberRepository->getActiveMembersByPoolIds($poolIds) as $member) {
            if ($member->user_id === $userId || !$member->pool) {
                continue;
            }
            $sharedPools[$member->user_id][$member->pool->id] = [
                'id'   => $member->pool->id,
                'name' => $member->pool->name,
            ];
        }

        if (empty($sharedPools)) {
            return new Collection();
        }

        $guesses = $this->guessRepository->getByMatchForUsers($matchId, array_keys($sharedPools));

        foreach ($guesses as $guess) {
            $guess->shared_pools = array_values($sharedPools[$guess->user_id] ?? []);
        }

        return $guesses;
    }

    public function getMemberGuesses(int $memberId, int $poolId): Collection
    {
        $member = $this->poolMemberRepository->getMemberById($poolId, $memberId);
        if (!$member) {
            throw new \Exception('Membro não encontrado neste bolão.', 404);
        }

        return $this->guessRepository->getByUser($member->user_id);
    }
}
