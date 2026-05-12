<?php

namespace App\Services\PoolMemberServices;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use App\Models\PoolMember;
use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use App\Services\LeaderboardServices\LeaderboardWriteService;
use Illuminate\Support\Facades\DB;

class PoolMemberWriteService
{
    public function __construct(
        private PoolMemberRepository $poolMemberRepository,
        private LeaderboardWriteService $leaderboardWriteService,
        private PoolMemberReadService $poolMemberReadService,
    ) {}

    public function addMember(int $poolId, PoolUserRole $role, int $userId): PoolMember
    {
        return DB::transaction(function () use ($poolId, $role, $userId) {
            $member = $this->poolMemberRepository->addMember($poolId, $role, $userId);
            $this->leaderboardWriteService->createEntry($poolId, $userId);

            return $member;
        });
    }

    public function updateRole(int $poolId, int $memberId, PoolUserRole $role, int $requesterId): void
    {
        $member = $this->poolMemberReadService->getMember($poolId, $memberId);

        if ($member->user_id === $requesterId) {
            throw new \Exception('Você não pode alterar o seu próprio papel', 403);
        }

        if ($member->role === PoolUserRole::OWNER) {
            throw new \Exception('Não é possível alterar o papel do proprietário do bolão', 403);
        }

        if ($member->role === $role) {
            throw new \Exception('O membro já possui o papel informado', 422);
        }

        $this->poolMemberRepository->updateRoleByMemberId($memberId, $role->value);
    }

    public function leavePool(int $poolId, int $userId): void
    {
        if ($this->poolMemberReadService->isOwner($poolId, $userId)) {
            throw new \Exception('O proprietário não pode sair do bolão', 403);
        }

        if (!$this->poolMemberReadService->isMember($poolId, $userId)) {
            throw new \Exception('Você não é membro deste bolão', 403);
        }

        DB::transaction(function () use ($poolId, $userId) {
            $this->poolMemberRepository->leavePool($poolId, $userId);
            $this->leaderboardWriteService->removeEntry($poolId, $userId);
        });
    }

    public function banMember(int $poolId, int $memberId, int $userId): void
    {
        $member = $this->poolMemberReadService->getMember($poolId, $memberId);

        if ($member->role === PoolUserRole::OWNER) {
            throw new \Exception('Não é possível banir o proprietário do bolão', 403);
        }

        if ($member->status === PoolMemberStatus::BANNED) {
            throw new \Exception('O membro já está banido', 422);
        }

        if ($member->role === PoolUserRole::ADMIN && !$this->poolMemberReadService->isOwner($poolId, $userId)) {
            throw new \Exception('Você não pode banir um administrador do bolão', 403);
        }

        DB::transaction(function () use ($poolId, $memberId, $member) {
            $this->poolMemberRepository->banMember($poolId, $memberId);
            $this->leaderboardWriteService->archiveEntry($poolId, $member->user_id);
        });
    }

    public function unbanMember(int $poolId, int $memberId, int $userId): void
    {
        $member = $this->poolMemberReadService->getMember($poolId, $memberId);

        if ($member->status !== PoolMemberStatus::BANNED) {
            throw new \Exception('O membro não está banido', 400);
        }

        if ($member->role === PoolUserRole::ADMIN && !$this->poolMemberReadService->isOwner($poolId, $userId)) {
            throw new \Exception('Você não pode desbanir um administrador do bolão', 403);
        }

        DB::transaction(function () use ($poolId, $memberId, $member) {
            $this->poolMemberRepository->unbanMember($poolId, $memberId);
            $this->leaderboardWriteService->restoreEntry($poolId, $member->user_id);
        });
    }
}
