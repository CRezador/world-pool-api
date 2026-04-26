<?php

namespace App\Services\PoolServices;

use App\Http\Enums\PoolUserRole;
use App\Models\Pool;
use App\Models\User;
use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use App\Repositories\PoolRepositories\PoolRepository;
use App\Services\PoolMemberServices\PoolMemberService;
use Illuminate\Database\Eloquent\Collection;

class PoolService
{
    public function __construct(
        private PoolRepository $poolRepository,
        private PoolMemberService $poolMemberService,
        private PoolMemberRepository $poolMemberRepository
    ) {

    }

    private function generateCode(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        do {
            for ($i = 0; $i < 6; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $i++;
            if ($i <= 5) {

                throw new \Exception('Não foi possível gerar um código de acesso único após várias tentativas. Tente novamente mais tarde.');
            }
        } while (Pool::where('join_code', $code)->exists());

        return $code;
    }

    public function showPublicPools(): Collection
    {
        return $this->poolRepository->getPublicPools();
    }

    public function showPool($id): Pool
    {
        return $this->poolRepository->getPool($id);
    }

    public function createPool(bool $is_public, User $user, string $name): Pool
    {
        $code = $this->generateCode();

        try {
            $pool = $this->poolRepository->createPool([
                'name' => $name,
                'join_code' => $code,
                'owner_id' => $user->id,
                'is_public' => $is_public,
            ]);
            $this->poolMemberService->addMember($pool->id, PoolUserRole::OWNER->value, $user->id);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao criar a Bolão: ' . $e->getMessage());
        }

        return $pool;
    }

    public function destroyPool($id, $userId): Pool
    {
        $pool = $this->poolRepository->getPool($id);

        if (!$pool) {
            throw new \Exception(1);
        }

        if ($pool->owner_id !== $userId) {
            throw new \Exception(2);
        }

        try {
            $deleted = $this->poolRepository->deletePool($pool->id);
            if (!$deleted) {
                throw new \Exception('Bolão não encontrado.');
            }
        } catch (\Exception $e) {
            throw new \Exception('Erro ao Remover Bolão: ' . $e->getMessage());
        }

        return $pool;
    }

    public function regenerateJoinCode($id): Pool
    {
        $pool = $this->poolRepository->getPool($id);

        if (!$pool) {
            throw new \Exception("Bolão não encontrado.");
        }
        $code = $this->generateCode();

        try {
            $pool->join_code = $code;
            $pool->save();
        } catch (\Exception $e) {
            throw new \Exception('Erro ao Regenerar Código de Acesso: ' . $e);
        }

        return $pool;
    }

    public function updatePool($id, array $data): Pool
    {
        $pool = $this->poolRepository->getPool($id);

        if (!$pool) {
            throw new \Exception("Bolão não encontrado.");
        }

        try {
            $poolUpdate = $this->poolRepository->updatePool($id, $data);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao Atualizar Bolão: ' . $e);
        }

        return $poolUpdate;
    }

    public function getPoolsByUserId(int $userId): Collection
    {
        return $this->poolRepository->getPoolsByUserId($userId);
    }

    public function getPoolByJoinCode(string $join_code): Pool
    {
        return $this->poolRepository->getPoolByJoinCode($join_code);
    }

    public function joinPool(string $join_code, int $userId): array
    {
        $pool = $this->poolRepository->getPoolByJoinCode($join_code);

        if (!$pool) {
            throw new \Exception('Bolão não encontrado.');
        }

        // Verificar se o usuário já é membro do bolão
        if ($this->poolMemberRepository->isMember($pool->id, $userId)) {
            throw new \Exception('Você já é membro deste bolão.');
        }

        // Adicionar o usuário como membro do bolão
        $member = $this->poolMemberRepository->addMember($pool->id, PoolUserRole::MEMBER->value, $userId);

        return [
            "Pool" => $pool,
            "Member" => $member
        ];
    }
}
