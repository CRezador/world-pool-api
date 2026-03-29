<?php

namespace App\Services\PoolServices;

use App\Models\Pool;
use App\Models\User;
use App\Repositories\PoolRepositories\PoolRepository;

class PoolService
{
    public function __construct(
        private PoolRepository $poolRepository,
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
            if ($i > 5) {
                throw new \Exception('Não foi possível gerar um código de acesso único após várias tentativas. Tente novamente mais tarde.');
            }
        } while (Pool::where('join_code', $code)->exists());

        return $code;
    }

    public function showPublicPools()
    {
        return $this->poolRepository->getPublicPools();
    }

    public function showPool($id)
    {
        return $this->poolRepository->getPool($id);
    }
    public function createPool(bool $is_public, User $user)
    {
        $code = $this->generateCode();

        try {
            $pool = $this->poolRepository->createPool([
                'name' => $user->name,
                'join_code' => $code,
                'owner_id' => $user->id,
                'is_public' => $is_public,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao criar a Bolão: ');
        }

        return $pool;
    }

    public function destroyPool($id, $userId)
    {
        $pool = $this->poolRepository->getPool($id);

        if (!$pool) {
            throw new \Exception(1);
        }

        if ($pool->owner_id !== $userId) {
            throw new \Exception(2);
        }

        try {
            $this->poolRepository->deletePool($pool->id);
        } catch (\Exception $e) {
            throw new \Exception('Erro ao Remover Bolão: ' . $e);
        }

        return $pool;
    }

    public function regenerateJoinCode($id, $ownerId)
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

    public function updatePool($id, $ownerId, array $data)
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
}
