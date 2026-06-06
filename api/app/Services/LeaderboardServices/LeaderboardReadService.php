<?php

namespace App\Services\LeaderboardServices;

use App\Models\Leaderboard;
use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\LeaderboardRepositories\LeaderboardRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaderboardReadService
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository,
        private GuessRepository $guessRepository,
    ) {}

    public function ranking(int $poolId): LengthAwarePaginator
    {
        $paginator = $this->leaderboardRepository->getPaginated($poolId, 20);
        $offset = ($paginator->currentPage() - 1) * $paginator->perPage();

        $paginator->getCollection()->each(function ($entry, $index) use ($offset) {
            $entry->position = $offset + $index + 1;
        });

        return $paginator;
    }

    public function top(int $poolId, int $limit = 3): Collection
    {
        $entries = $this->leaderboardRepository->getTop($poolId, $limit);

        $entries->each(function ($entry, $index) {
            $entry->position = $index + 1;
        });

        return $entries;
    }

    public function myPosition(int $poolId, int $userId): ?Leaderboard
    {
        $entry = $this->leaderboardRepository->getByUser($poolId, $userId);

        if ($entry) {
            $entry->last_results = $this->guessRepository->getLastScoredByUserAndPool($userId, $poolId);
        }

        return $entry;
    }

    public function show(int $poolId, int $userId): Leaderboard
    {
        return $this->findEntryOrFail($poolId, $userId);
    }

    public function myStats(int $userId): array
    {
        $row = $this->leaderboardRepository->getStatsByUser($userId);

        return [
            'pools_count'      => (int) $row->pools_count,
            'total_points'     => (int) $row->total_points,
            'total_exact_hits' => (int) $row->total_exact_hits,
            'total_result_hits' => (int) $row->total_result_hits,
            'total_guesses'    => (int) $row->total_guesses,
            'best_rank'        => $row->best_rank !== null ? (int) $row->best_rank : null,
        ];
    }

    private function findEntryOrFail(int $poolId, int $userId): Leaderboard
    {
        $entry = $this->leaderboardRepository->getByUser($poolId, $userId);

        if (!$entry) {
            throw new ModelNotFoundException('Participante não encontrado no ranking.');
        }

        return $entry;
    }
}
