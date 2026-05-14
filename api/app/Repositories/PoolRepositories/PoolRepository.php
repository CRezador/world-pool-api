<?php

namespace App\Repositories\PoolRepositories;

use App\Models\Pool;
use Illuminate\Database\Eloquent\Collection;

class PoolRepository
{
    public function getPublicPools(): Collection
    {
        return Pool::with('owner')->where('is_public', true)->get();
    }

    public function getPool(int $id): ?Pool
    {
        return Pool::with('owner')->find($id);
    }

    public function getPoolByJoinCode(string $join_code): ?Pool
    {
        return Pool::with('owner')->where('join_code', $join_code)->first();
    }

    public function createPool(array $pool): Pool
    {
        return Pool::create($pool);
    }

    public function deletePool(int $id): bool
    {
        return Pool::where('id', '=', $id)->delete() > 0;
    }

    public function updatePool(int $id, array $data): Pool
    {
        Pool::where('id', $id)->update($data);

        return Pool::with('owner')->find($id);
    }

    public function getPoolsByUserId(int $userId): Collection
    {
        return Pool::with('owner')
            ->select('pools.*')
            ->join('pool_members', 'pool_members.pool_id', '=', 'pools.id')
            ->where('pool_members.user_id', $userId)
            ->where('pool_members.status', 'ACTIVE')
            ->get();
    }
}
