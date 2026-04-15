<?php

namespace App\Repositories\PoolMemberRepositories;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use App\Models\Pool;
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

    public function isAdmin($poolId, $userId)
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->where('role', PoolUserRole::ADMIN->value)
            ->exists();
    }

    public function addMember($pool, $role, $userId)
    {
        PoolMembers::create([
            'pool_id' => $pool->id,
            'user_id' => $userId,
            'role' => $role,
            'status' => PoolMemberStatus::ACTIVE->value,
            'joined_at' => now(),
        ]);
    }

    public function getMembersByPoolId($poolId)
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->with('user:id,name') // Carrega os dados do usuário relacionado, selecionando apenas id e name
            ->get();
    }
}
