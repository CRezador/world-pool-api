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

    public function createPool(array $pool): Pool
    {
        return Pool::create($pool);
    }

    public function deletePool($id)
    {
        return Pool::where('id', '=', $id)->delete();
    }
}
