<?php

namespace App\Services\LeaderboardServices;

use App\Models\Leaderboard;
use App\Repositories\LeaderboardRepositories\LeaderboardRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaderboardReadService
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository,
    ) {}

    public function ranking(int $poolId): LengthAwarePaginator
    {
        $paginator = $this->leaderboardRepository->getPaginated($poolId, 20);
        $offset = ($paginator->currentPage() - 1) * $paginator->perPage();

        $paginator->getCollection()->each(function ($entry, $index) use ($offset) {
            $entry->rank = $offset + $index + 1;
        });

        return $paginator;
    }

    public function top(int $poolId, int $limit = 3): Collection
    {
        if ($limit < 1 || $limit > 10) {
            throw new \InvalidArgumentException('O limite deve ser entre 1 e 10', 422);
        }

        $entries = $this->leaderboardRepository->getTop($poolId, $limit);

        $entries->each(function ($entry, $index) {
            $entry->rank = $index + 1;
        });

        return $entries;
    }

    public function myPosition(int $poolId, int $userId): ?Leaderboard
    {
        $entry = $this->leaderboardRepository->getByUser($poolId, $userId);

        if (!$entry) {
            return null;
        }

        $entry->rank = $this->leaderboardRepository->getRankPosition($poolId, $userId);

        return $entry;
    }

    public function show(int $poolId, int $userId): Leaderboard
    {
        $entry = $this->findEntryOrFail($poolId, $userId);
        $entry->rank = $this->leaderboardRepository->getRankPosition($poolId, $userId);

        return $entry;
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
