<?php

namespace App\Services\PoolServices;

use App\Http\Requests\Pool\PoolRequest;
use App\Models\Pool;
use App\Repositories\PoolRepositories\PoolRepository;


class PoolService{

    public function __construct(
        private PoolRepository $poolRepository
    )
    {
        
    }

    private function generateCode(): string{
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }

    public function showPublicPools(){
        return $this->poolRepository->getPublicPools();
    }

    public function showPool($id){
        return $this->poolRepository->getPool($id);
    }
    public function createPool(PoolRequest $request){
        do {
            $code = $this->generateCode();
        } while (Pool::where('join_code', $code)->exists());

        try{
            $pool = $this->poolRepository->createPool([
                'name' => $request->name,
                'join_code' => $code,
                'owner_id' => $request->user()->id,
                'is_public' => $request->is_public,
            ]);
        }catch(\Exception $e){
            throw new \Exception('Erro ao criar a Bolão: ' . $e->getMessage());
        }

        return $pool;
    }
}