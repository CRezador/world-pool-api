<?php

namespace App\Services\PoolMemberServices;

use App\Models\PoolMembers;
use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use Illuminate\Database\Eloquent\Collection;

class PoolMemberService
{
    public function __construct(
        private PoolMemberRepository $poolMemberRepository,
    ) {

    }

    public function addMember(int $poolId, string $role, int $userId): PoolMembers
    {
        return $this->poolMemberRepository->addMember($poolId, $role, $userId);
    }

    public function listMembers(int $poolId): string
    {
        return $this->poolMemberRepository->getMembersByPoolId($poolId);
    }

    public function isMember(int $poolId, int $userId): PoolMembers
    {
        return $this->poolMemberRepository->isMember($poolId, $userId);
    }

    public function isAdmin(int $poolId, int $userId): PoolMembers
    {
        return $this->poolMemberRepository->isAdmin($poolId, $userId);
    }

    public function getPoolsByUserId(int $userId): Collection
    {
        return $this->poolMemberRepository->getPoolsByUserId($userId);
    }

}
