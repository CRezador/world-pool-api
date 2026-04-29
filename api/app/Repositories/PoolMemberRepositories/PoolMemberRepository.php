<?php

namespace App\Repositories\PoolMemberRepositories;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use App\Models\Pool;
use App\Models\PoolMembers;
use Illuminate\Database\Eloquent\Collection;

class PoolMemberRepository
{
    public function isMember(int $poolId, int $userId): bool
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function isAdmin(int $poolId, int $userId): bool
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->where('role', PoolUserRole::ADMIN->value)
            ->exists();
    }

    public function isOwner(int $poolId, int $userId): bool
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->where('role', PoolUserRole::OWNER->value)
            ->exists();
    }

    public function isOwnerByMemberId(int $poolId, int $memberId): bool
    {
        return PoolMembers::query()
            ->where('id', $memberId)
            ->where('pool_id', $poolId)
            ->where('role', PoolUserRole::OWNER->value)
            ->exists();
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

    public function getMembersByPoolId(int $poolId): Collection
    {
        return PoolMembers::query()
            ->where('pool_id', $poolId)
            ->with('user:id,name') // Carrega os dados do usuário relacionado, selecionando apenas id e name
            ->get();
    }

    public function getPoolsByUserId(int $userId): Collection
    {
        //pegue todos os Pool onde o userId é membro em poolmembers e retorne os Pool relacionados
        return Pool::query()
            ->whereHas('members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();
    }

    public function updateRole(int $poolId, int $userId, string $role): void
    {
        PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->update(['role' => $role]);

    }

    public function getRole(int $poolId, int $userId): PoolUserRole|null
    {
        $member = PoolMembers::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->first();

        return $member ? $member->role : null;
    }
}
