<?php

namespace App\Services\LeaderboardServices;

use App\Models\Leaderboard;
use App\Repositories\LeaderboardRepositories\LeaderboardRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LeaderboardReadService
{
    public function __construct(
        private LeaderboardRepository $leaderboardRepository,
    ) {}

    public function ranking(int $poolId): LengthAwarePaginator
    {
        return $this->leaderboardRepository->getPaginated($poolId, 20);
    }

    public function top(int $poolId, int $limit = 3): Collection
    {
        if($limit < 3) {
            $limit = 3;
        }

        if($limit > 10){
            throw new \InvalidArgumentException('O limite deve ser entre 3 e 10', 422);
        }

        return $this->leaderboardRepository->getTop($poolId, $limit);
    }

    public function getByUser(int $poolId, int $userId): ?Leaderboard
    {
        return $this->leaderboardRepository->getByUser($poolId, $userId);
    }

    public function getRankPosition(int $poolId, int $userId): int
    {
        return $this->leaderboardRepository->getRankPosition($poolId, $userId);
    }
}
