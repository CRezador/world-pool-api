<?php

namespace App\Repositories\PoolMemberRepositories;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use App\Models\PoolMember;
use Illuminate\Database\Eloquent\Collection;

class PoolMemberRepository
{
    public function isBanned(int $poolId, int $userId): bool
    {
        return PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->where('status', PoolMemberStatus::BANNED->value)
            ->exists();
    }

    public function isMember(int $poolId, int $userId): bool
    {
        return PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->where('status', PoolMemberStatus::ACTIVE->value)
            ->exists();
    }

    public function isAdmin(int $poolId, int $userId): bool
    {
        return PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->where('role', PoolUserRole::ADMIN->value)
            ->where('status', PoolMemberStatus::ACTIVE->value)
            ->exists();
    }

    public function isOwner(int $poolId, int $userId): bool
    {
        return PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->where('role', PoolUserRole::OWNER->value)
            ->exists();
    }

    public function isOwnerByMemberId(int $poolId, int $memberId): bool
    {
        return PoolMember::query()
            ->where('id', $memberId)
            ->where('pool_id', $poolId)
            ->where('role', PoolUserRole::OWNER->value)
            ->exists();
    }

    public function addMember(int $poolId, PoolUserRole $role, int $userId): PoolMember
    {
        return PoolMember::create([
            'pool_id' => $poolId,
            'user_id' => $userId,
            'role' => $role->value,
            'status' => PoolMemberStatus::ACTIVE->value,
            'joined_at' => now(),
        ]);
    }

    public function getMemberByUserId(int $poolId, int $userId): ?PoolMember
    {
        return PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->first();
    }

    public function getMembersByPoolId(int $poolId, ?PoolMemberStatus $status = null): Collection
    {
        return PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('status', ($status ?? PoolMemberStatus::ACTIVE)->value)
            ->with('user:id,name')
            ->get();
    }

    public function updateRole(int $poolId, int $userId, string $role): void
    {
        PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->update(['role' => $role]);
    }

    public function updateRoleByMemberId(int $memberId, string $role): void
    {
        PoolMember::query()
            ->where('id', $memberId)
            ->update(['role' => $role]);
    }

    public function getMemberById(int $poolId, int $memberId): ?PoolMember
    {
        return PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('id', $memberId)
            ->with('user:id,name')
            ->first();
    }

    public function leavePool(int $poolId, int $userId): void
    {
        PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->update(['status' => PoolMemberStatus::LEFT->value]);
    }

    public function getRole(int $poolId, int $userId): ?PoolUserRole
    {
        $member = PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('user_id', $userId)
            ->first();

        return $member ? $member->role : null;
    }

    public function getRoleByMemberId(int $poolId, int $memberId): ?PoolUserRole
    {
        $member = PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('id', $memberId)
            ->first();

        return $member ? $member->role : null;
    }

    public function banMember(int $poolId, int $memberId): void
    {
        PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('id', $memberId)
            ->update(['status' => PoolMemberStatus::BANNED->value]);
    }

    public function unbanMember(int $poolId, int $memberId): void
    {
        PoolMember::query()
            ->where('pool_id', $poolId)
            ->where('id', $memberId)
            ->update(['status' => PoolMemberStatus::ACTIVE->value]);
    }

}
