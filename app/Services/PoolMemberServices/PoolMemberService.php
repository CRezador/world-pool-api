<?php

namespace App\Services\PoolMemberServices;

use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use App\Repositories\PoolRepositories\PoolRepository;

class PoolMemberService
{
    public function __construct(
        private PoolMemberRepository $poolMemberRepository,
        private PoolRepository $poolRepository
    ) {

    }

    public function joinPool($join_code, $user)
    {
        $pool = $this->poolRepository->getPoolByJoinCode($join_code);

        if (!$pool) {
            throw new \Exception('Bolão não encontrado.');
        }

        // Verificar se o usuário já é membro do bolão
        if ($this->poolMemberRepository->isMember($pool->id, $user->id)) {
            throw new \Exception('Você já é membro deste bolão.');
        }

        // Adicionar o usuário como membro do bolão
        $this->poolMemberRepository->addMember($pool->id, $user->id);

        return $pool;
    }
}
