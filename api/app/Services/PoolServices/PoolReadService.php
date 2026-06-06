<?php

namespace App\Services\PoolServices;

use App\Models\Pool;
use App\Repositories\GuessRepositories\GuessRepository;
use App\Repositories\LeaderboardRepositories\LeaderboardRepository;
use App\Repositories\PoolRepositories\PoolRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PoolReadService
{
    public function __construct(
        private PoolRepository $poolRepository,
        private LeaderboardRepository $leaderboardRepository,
        private GuessRepository $guessRepository,
    ) {}

    public function showPublicPools(int $perPage = 10, int $userId = 0): LengthAwarePaginator
    {
        return $this->poolRepository->getPublicPools($perPage, $userId);
    }

    public function showPool(int $id): ?Pool
    {
        $pool = $this->poolRepository->getPool($id);

        if ($pool) {
            $pool->leader = $this->leaderboardRepository->getLeader($pool->id);
        }

        return $pool;
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

            $pool->my_points    = $entry?->points ?? 0;
            $pool->my_rank      = $entry?->position;
            $pool->last_results = $this->guessRepository->getLastScoredByUserAndPool($userId, $pool->id);
            $pool->leader       = $this->leaderboardRepository->getLeader($pool->id);
        }

        return $pools;
    }

    public function getPoolByJoinCode(string $join_code): ?Pool
    {
        return $this->poolRepository->getPoolByJoinCode($join_code);
    }
}
