<?php

namespace App\Repositories\PoolMemberRepositories;

use App\Http\Enums\PoolUserRole;
use App\Models\PoolMembers;

class PoolMemberRepository
{
    public function isMember($poolId, $userId)
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function addMember($poolId, $userId)
    {
        PoolMembers::create([
            'pool_id' => $poolId,
            'user_id' => $userId,
            'role' => PoolUserRole::MEMBER->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
