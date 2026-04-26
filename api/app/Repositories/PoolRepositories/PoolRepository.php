<?php

namespace App\Repositories\PoolRepositories;

use App\Models\Pool;
use Illuminate\Database\Eloquent\Collection;

class PoolRepository
{
    public function getPublicPools(): Collection
    {
        return Pool::select()->where('is_public', true)->get();
    }

    public function getPool($id): Pool
    {
        return Pool::query()->find($id);
    }

    public function getPoolByJoinCode($join_code): Pool
    {
        return Pool::query()->where('join_code', $join_code)->first();
    }

    public function createPool(array $pool): Pool
    {
        return Pool::create($pool);
    }

    public function deletePool($id): bool
    {
        return Pool::where('id', '=', $id)->delete() > 0;
    }

    public function updatePool($id, array $data): Pool
    {
        Pool::where('id', $id)->update($data);
        return Pool::query()->find($id);
    }

    public function getPoolsByUserId(int $userId): Collection
    {
        return Pool::query()
        ->select('pools.*')
        ->join('pool_members', 'pool_members.pool_id', '=', 'pools.id')
        ->where('pool_members.user_id', $userId)
        ->where('pool_members.status', 'ACTIVE')
        ->get();
    }
}
