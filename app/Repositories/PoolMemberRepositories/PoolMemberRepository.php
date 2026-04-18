<?php

namespace App\Repositories\PoolMemberRepositories;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use App\Models\Pool;
use App\Models\PoolMembers;

class PoolMemberRepository
{
    public function isMember(int $poolId, int $userId)
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function isAdmin(int $poolId, int $userId): PoolMembers
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->where('role', PoolUserRole::ADMIN->value)
            ->first();
    }

    public function addMember(int $poolId, string $role, int $userId): PoolMembers
    {
        return PoolMembers::create([
            'pool_id' => $poolId,
            'user_id' => $userId,
            'role' => $role,
            'status' => PoolMemberStatus::ACTIVE->value,
            'joined_at' => now(),
        ]);
    }

    public function getMembersByPoolId(int $poolId): string
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->with('user:id,name') // Carrega os dados do usuário relacionado, selecionando apenas id e name
            ->get();
    }

    public function getPoolsByUserId(int $userId)
    {
        //pegue todos os Pool onde o userId é membro em poolmembers e retorne os Pool relacionados
        return Pool::query()
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
    }
}
