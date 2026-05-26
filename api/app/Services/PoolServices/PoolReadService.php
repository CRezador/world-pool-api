<?php

namespace App\Services\PoolServices;

use App\Models\Pool;
use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\LeaderboardRepositories\LeaderboardRepository;
use App\Repositories\PoolRepositories\PoolRepository;
use Illuminate\Database\Eloquent\Collection;

class PoolReadService
{
    public function __construct(
        private PoolRepository $poolRepository,
        private LeaderboardRepository $leaderboardRepository,
        private GuessRepository $guessRepository,
    ) {}

    public function showPublicPools(): Collection
    {
        return $this->poolRepository->getPublicPools();
    }

    public function showPool(int $id): ?Pool
    {
        return $this->poolRepository->getPool($id);
    }

    public function getPoolsByUserId(int $userId): Collection
    {
        return $this->poolRepository->getPoolsByUserId($userId);
    }

    public function getMyPoolsWithStats(int $userId): Collection
    {
        $pools = $this->poolRepository->getPoolsByUserId($userId);

        foreach ($pools as $pool) {
            $entry = $this->leaderboardRepository->getByUser($pool->id, $userId);

            $pool->my_points = $entry?->points ?? 0;
            $pool->my_rank   = $entry ? $this->leaderboardRepository->getRankPosition($entry) : null;
            $pool->last_results = $this->guessRepository->getLastScoredByUserAndPool($userId, $pool->id);
        }

        return $pools;
    }

    public function getPoolByJoinCode(string $join_code): ?Pool
    {
        return $this->poolRepository->getPoolByJoinCode($join_code);
    }
}
