<?php

namespace App\Services\LeaderboardServices;

use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\LeaderboardRepositories\LeaderboardRepository;

class LeaderboardWriteService
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository,
        private GuessRepository $guessRepository,
    ) {}

    public function createEntry(int $poolId, int $userId): void
    {
        $this->leaderboardRepository->createEntry($poolId, $userId);
    }

    public function removeEntry(int $poolId, int $userId): void
    {
        $this->leaderboardRepository->deleteEntry($poolId, $userId);
    }

    public function archiveEntry(int $poolId, int $userId): void
    {
        $this->leaderboardRepository->archiveEntry($poolId, $userId);
    }

    public function restoreEntry(int $poolId, int $userId): void
    {
        $this->leaderboardRepository->restoreEntry($poolId, $userId);
    }

    //agrega todos os guesses pontuados do usuário no bolão (points, exact_hits, result_hits, guesses_count) e atualiza a entrada via updateStats; chamado pelo GuessScoringService
    public function syncUser(int $poolId, int $userId): void
    {
        $stats = $this->leaderboardRepository->getStatsByUser($poolId, $userId);

        $this->leaderboardRepository->updateStats($poolId, $userId, $stats);
    }

    public function rebuild(int $poolId): void
    {
        $entries = $this->leaderboardRepository->getAllByPool($poolId);

        foreach ($entries as $entry) {
            $stats = $this->leaderboardRepository->getStatsByUser($poolId, $entry->user_id);
            $this->leaderboardRepository->updateStats($poolId, $entry->user_id, $stats);
        }
    } 
}
