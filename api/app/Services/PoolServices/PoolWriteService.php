<?php

namespace App\Services\PoolServices;

use App\Http\Enums\PoolUserRole;
use App\Models\Pool;
use App\Models\User;
use App\Repositories\PoolRepositories\PoolRepository;
use App\Services\PoolMemberServices\PoolMemberReadService;
use App\Services\PoolMemberServices\PoolMemberWriteService;
use Illuminate\Support\Str;

class PoolWriteService
{
    public function __construct(
        private PoolRepository $poolRepository,
        private PoolMemberReadService $poolMemberReadService,
        private PoolMemberWriteService $poolMemberWriteService,
    ) {}

    private function generateCode(): string
    {
        $attempts = 0;

        do {
            $code = strtoupper(Str::random(6));
            if (++$attempts > 10) {
                throw new \Exception('Não foi possível gerar um código de acesso único após várias tentativas. Tente novamente mais tarde.');
            }
        } while (Pool::where('join_code', $code)->exists());

        return $code;
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
            $this->poolMemberWriteService->addMember($pool->id, PoolUserRole::OWNER, $user->id);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao criar o Bolão: ' . $e->getMessage());
        }

        return $pool;
    }

    public function destroyPool(int $id, int $userId): Pool
    {
        $pool = $this->poolRepository->getPool($id);

        if (!$pool) {
            throw new \Exception('Bolão não encontrado.');
        }

        if ($pool->owner_id !== $userId) {
            throw new \Exception('Apenas o proprietário do bolão pode removê-lo.');
        }

        $this->poolRepository->deletePool($pool->id);

        return $pool;
    }

    public function regenerateJoinCode(int $id): Pool
    {
        $pool = $this->poolRepository->getPool($id);

        if (!$pool) {
            throw new \Exception('Bolão não encontrado.');
        }

        $code = $this->generateCode();

        try {
            $pool = $this->poolRepository->updatePool($id, ['join_code' => $code]);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao Regenerar Código de Acesso: ' . $e->getMessage());
        }

        return $pool;
    }

    public function updatePool(int $id, array $data): Pool
    {
        $pool = $this->poolRepository->getPool($id);

        if (!$pool) {
            throw new \Exception('Bolão não encontrado.');
        }

        try {
            $poolUpdate = $this->poolRepository->updatePool($id, $data);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao Atualizar Bolão: ' . $e->getMessage());
        }

        return $poolUpdate;
    }

    public function joinPool(string $join_code, int $userId): array
    {
        $pool = $this->poolRepository->getPoolByJoinCode($join_code);

        if (!$pool) {
            throw new \Exception('Bolão não encontrado.');
        }

        if ($this->poolMemberReadService->isBanned($pool->id, $userId)) {
            throw new \Exception('Você está banido deste bolão.');
        }

        if ($this->poolMemberReadService->isMember($pool->id, $userId)) {
            throw new \Exception('Você já é membro deste bolão.');
        }

        $member = $this->poolMemberWriteService->addMember($pool->id, PoolUserRole::MEMBER, $userId);

        return [
            'Pool' => $pool,
            'Member' => $member,
        ];
    }
}
