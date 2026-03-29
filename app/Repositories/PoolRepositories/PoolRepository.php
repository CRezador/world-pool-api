<?php

namespace App\Repositories\PoolRepositories;

use App\Models\Pool;

class PoolRepository
{
    public function getPublicPools()
    {
        return Pool::select()->where('is_public', true)->get();
    }

    public function getPool($id)
    {
        return Pool::query()->find($id);
    }

    public function getPoolByJoinCode($join_code)
    {
        return Pool::query()->where('join_code', $join_code)->first();
    }

    public function createPool(array $pool): Pool
    {
        return Pool::create($pool);
    }

    public function deletePool($id)
    {
        return Pool::where('id', '=', $id)->delete();
    }

    public function updatePool($id, array $data)
    {
        return Pool::where('id', $id)->update($data);
    }
}
