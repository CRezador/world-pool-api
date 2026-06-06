<?php

namespace App\Repositories\PoolRepositories;

use App\Models\Pool;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PoolRepository
{
    public function getPublicPools(int $perPage = 10, int $userId = 0): LengthAwarePaginator
    {
        return Pool::with('owner')
            ->withCount(['members as members_count' => fn($q) => $q->where('status', 'ACTIVE')])
            ->withExists(['members as is_member' => fn($q) => $q->where('user_id', $userId)->where('status', 'ACTIVE')])
            ->where('is_public', true)
            ->paginate($perPage);
    }

    public function getPool(int $id): ?Pool
    {
        return Pool::with('owner')
            ->withCount(['members as members_count' => fn($q) => $q->where('status', 'ACTIVE')])
            ->find($id);
    }

    public function getPoolByJoinCode(string $join_code): ?Pool
    {
        return Pool::with('owner')
            ->withCount(['members as members_count' => fn($q) => $q->where('status', 'ACTIVE')])
            ->where('join_code', $join_code)
            ->first();
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
            ->withCount(['members as members_count' => fn($q) => $q->where('status', 'ACTIVE')])
            ->whereHas('members', fn($q) => $q->where('user_id', $userId)->where('status', 'ACTIVE'))
            ->get();
    }
}
