<?php

namespace App\Services\PoolMemberServices;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use App\Models\PoolMember;
use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use Illuminate\Database\Eloquent\Collection;

class PoolMemberReadService
{
    public function __construct(private PoolMemberRepository $poolMemberRepository) {}

    public function listMembers(int $poolId, ?PoolMemberStatus $status = null): Collection
    {
        return $this->poolMemberRepository->getMembersByPoolId($poolId, $status);
    }

    public function getAuthMember(int $poolId, int $userId): PoolMember
    {
        $member = $this->poolMemberRepository->getMemberByUserId($poolId, $userId);

        if (!$member) {
            throw new \Exception('Membro não encontrado no bolão', 404);
        }

        return $member;
    }

    public function getMember(int $poolId, int $memberId): PoolMember
    {
        $member = $this->poolMemberRepository->getMemberById($poolId, $memberId);

        if (!$member) {
            throw new \Exception('Membro não encontrado no bolão');
        }

        return $member;
    }

    public function isBanned(int $poolId, int $userId): bool
    {
        return $this->poolMemberRepository->isBanned($poolId, $userId);
    }

    public function isMember(int $poolId, int $userId): bool
    {
        return $this->poolMemberRepository->isMember($poolId, $userId);
    }

    public function isOwner(int $poolId, int $userId): bool
    {
        return $this->poolMemberRepository->isOwner($poolId, $userId);
    }

    public function isOwnerByMemberId(int $poolId, int $memberId): bool
    {
        return $this->poolMemberRepository->isOwnerByMemberId($poolId, $memberId);
    }

    public function isAdmin(int $poolId, int $userId): bool
    {
        return $this->poolMemberRepository->isAdmin($poolId, $userId);
    }

    public function getRole(int $poolId, int $userId): ?PoolUserRole
    {
        return $this->poolMemberRepository->getRole($poolId, $userId);
    }

    public function getRoleByMemberId(int $poolId, int $memberId): ?PoolUserRole
    {
        return $this->poolMemberRepository->getRoleByMemberId($poolId, $memberId);
    }
}
