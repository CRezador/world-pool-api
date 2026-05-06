<?php

namespace App\Services\PoolMemberServices;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use App\Models\PoolMembers;
use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use Illuminate\Database\Eloquent\Collection;

class PoolMemberService
{
    public function __construct(
        private PoolMemberRepository $poolMemberRepository,
    ) {}

    public function addMember(int $poolId, string $role, int $userId): PoolMembers
    {
        return $this->poolMemberRepository->addMember($poolId, $role, $userId);
    }

    public function listMembers(int $poolId): Collection
    {
        return $this->poolMemberRepository->getMembersByPoolId($poolId);
    }

    public function getMember(int $poolId, int $memberId): PoolMembers
    {
        $member = $this->poolMemberRepository->getMemberById($poolId, $memberId);

        if (!$member) {
            throw new \Exception('Membro não encontrado no bolão');
        }

        return $member;
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

    public function getRole(int $poolId, int $userId): PoolUserRole
    {
        return $this->poolMemberRepository->getRole($poolId, $userId);
    }

    public function updateRole(int $poolId, int $userId, PoolUserRole $role): void
    {

        if ($this->isOwner($poolId, $userId)) {
            throw new \Exception('Não é possível alterar o papel do proprietário do bolão');
        }

        if (!$this->isMember($poolId, $userId)) {
            throw new \Exception('Membro não encontrado no bolão');
        }

        $memberRole = $this->getRole($poolId, $userId);
        if ($memberRole->value === $role->value) {
            throw new \Exception('O membro já possui o papel informado');
        }

        $this->poolMemberRepository->updateRole($poolId, $userId, $role->value);
    }

    public function leavePool(int $poolId, int $userId): void
    {
        if ($this->isOwner($poolId, $userId)) {
            throw new \Exception('O proprietário não pode sair do bolão');
        }

        if (!$this->isMember($poolId, $userId)) {
            throw new \Exception('Você não é membro deste bolão');
        }

        $this->poolMemberRepository->leavePool($poolId, $userId);
    }

    public function getRoleByMemberId(int $poolId, int $memberId): ?PoolUserRole
    {
        return $this->poolMemberRepository->getRoleByMemberId($poolId, $memberId);
    }

    public function banMember(int $poolId, int $memberId, int $userId): void
    {
        $member = $this->poolMemberRepository->getMemberById($poolId, $memberId);

        if (!$member) {
            throw new \Exception('Membro não encontrado no bolão', 404);
        }

        if ($member->role === PoolUserRole::OWNER) {
            throw new \Exception('Não é possível banir o proprietário do bolão', 403);
        }

        if ($member->role === PoolUserRole::ADMIN && !$this->isOwner($poolId, $userId)) {
            throw new \Exception('Você não pode banir um administrador do bolão', 403);
        }

        $this->poolMemberRepository->banMember($poolId, $memberId);
    }

    public function unbanMember(int $poolId, int $memberId, int $userId): void
    {
        $member = $this->poolMemberRepository->getMemberById($poolId, $memberId);

        if (!$member) {
            throw new \Exception('Membro não encontrado no bolão', 404);
        }

        if ($member->status !== PoolMemberStatus::BANNED) {
            throw new \Exception('O membro não está banido', 400);
        }

        if ($member->role === PoolUserRole::ADMIN && !$this->isOwner($poolId, $userId)) {
            throw new \Exception('Você não pode desbanir um administrador do bolão', 403);
        }

        $this->poolMemberRepository->unbanMember($poolId, $memberId);
    }
}
