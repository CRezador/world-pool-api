<?php

namespace App\Services\PoolServices;

use App\Models\Pool;
use App\Repositories\PoolRepositories\PoolRepository;
use Illuminate\Database\Eloquent\Collection;

class PoolReadService
{
    public function __construct(private PoolRepository $poolRepository) {}

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

    public function getPoolByJoinCode(string $join_code): ?Pool
    {
        return $this->poolRepository->getPoolByJoinCode($join_code);
    }
}
