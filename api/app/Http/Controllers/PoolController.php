<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pool\PoolJoinRequest;
use App\Http\Requests\Pool\PoolRequest;
use App\Http\Requests\Pool\PoolUpdateRequest;
use App\Http\Transformers\PoolMemberTransformers\PoolMemberTransformer;
use App\Http\Transformers\PoolTransformers\PoolTransformer;
use App\Services\PoolServices\PoolReadService;
use App\Services\PoolServices\PoolWriteService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class PoolController extends Controller
{
    public function __construct(
        private PoolReadService $poolReadService,
        private PoolWriteService $poolWriteService,
        private PoolTransformer $poolTransformer,
        private PoolMemberTransformer $poolMemberTransformer
    ) {}

    #[OA\Get(
        path: '/api/pools',
        summary: 'Lista bolões públicos disponíveis',
        security: [['sanctum' => []]],
        tags: ['Pools'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de bolões públicos'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(): Response
    {
        return response()->json(
            $this->poolTransformer->collection($this->poolReadService->showPublicPools(), 'Lista de bolões públicos'),
            200
        );
    }

    #[OA\Get(
        path: '/api/me/pools',
        summary: 'Lista os bolões do usuário autenticado',
        security: [['sanctum' => []]],
        tags: ['Pools'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de bolões do usuário'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function myPools(Request $request): Response
    {
        $pools = $this->poolReadService->getPoolsByUserId($request->user()->id);

        return response()->json(
            $this->poolTransformer->collection($pools, 'Lista de bolões do usuário'),
            200
        );
    }

    #[OA\Post(
        path: '/api/pools',
        summary: 'Cria um novo bolão',
        security: [['sanctum' => []]],
        tags: ['Pools'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'is_public'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Bolão da Galera'),
                    new OA\Property(property: 'is_public', type: 'boolean', example: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Bolão criado'),
            new OA\Response(response: 400, description: 'Erro ao criar bolão'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function store(PoolRequest $request): Response
    {
        $validated = $request->validated();

        try {
            $is_public = $validated['is_public'];
            $name = $validated['name'];
            $pool = $this->poolWriteService->createPool($is_public, $request->user(), $name);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json(
            $this->poolTransformer->item($pool, 'Bolão criado'),
            201
        );
    }

    #[OA\Get(
        path: '/api/pools/{id}',
        summary: 'Retorna detalhes de um bolão',
        security: [['sanctum' => []]],
        tags: ['Pools'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Bolão encontrado'),
            new OA\Response(response: 404, description: 'Bolão não encontrado'),
        ]
    )]
    public function show(int $id): Response
    {
        $pool = $this->poolReadService->showPool($id);

        if (!$pool) {
            return response()->json(['message' => 'Bolão não encontrado'], 404);
        }

        return response()->json(
            $this->poolTransformer->item($pool, 'Bolão Encontrado'),
            200
        );
    }

    #[OA\Delete(
        path: '/api/pools/{id}',
        summary: 'Remove um bolão (somente owner)',
        security: [['sanctum' => []]],
        tags: ['Pools'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Bolão removido'),
            new OA\Response(response: 403, description: 'Acesso negado'),
            new OA\Response(response: 404, description: 'Bolão não encontrado'),
        ]
    )]
    public function destroy(int $id, Request $request): Response
    {
        $pool = $this->poolWriteService->destroyPool($id, $request->user()->id);

        return response()->json(
            $this->poolTransformer->item($pool, 'Bolão removido'),
            200
        );
    }

    #[OA\Post(
        path: '/api/pools/{poolId}/regenerate-code',
        summary: 'Regenera o código de acesso do bolão (admin/owner)',
        security: [['sanctum' => []]],
        tags: ['Pools'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Código regenerado'),
            new OA\Response(response: 403, description: 'Acesso negado'),
            new OA\Response(response: 404, description: 'Bolão não encontrado'),
        ]
    )]
    public function regenerateJoinCode(int $poolId, Request $request): Response
    {
        $pool = $this->poolReadService->showPool($poolId);

        if (!$pool) {
            return response()->json(['message' => 'Bolão não encontrado.'], 404);
        }

        if ($request->user()->id !== $pool->owner_id) {
            return response()->json([
                'message' => 'Apenas o proprietário do bolão pode regenerar o código de acesso.',
            ], 403);
        }

        try {
            $pool = $this->poolWriteService->regenerateJoinCode($poolId);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json(
            $this->poolTransformer->item($pool, 'Código de acesso regenerado'),
            200
        );
    }

    #[OA\Put(
        path: '/api/pools/{id}',
        summary: 'Atualiza dados do bolão (owner)',
        security: [['sanctum' => []]],
        tags: ['Pools'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'is_public', type: 'boolean'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Bolão atualizado'),
            new OA\Response(response: 400, description: 'Erro ao atualizar'),
            new OA\Response(response: 403, description: 'Acesso negado'),
        ]
    )]
    public function update(PoolUpdateRequest $request, int $id): Response
    {
        try {
            $pool = $this->poolWriteService->updatePool($id, $request->validated());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json(
            $this->poolTransformer->item($pool, 'Bolão atualizado'),
            200
        );
    }

    #[OA\Post(
        path: '/api/pools/join',
        summary: 'Usuário entra em um bolão usando join_code',
        security: [['sanctum' => []]],
        tags: ['Pools'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['join_code'],
                properties: [
                    new OA\Property(property: 'join_code', type: 'string', example: 'ABC123'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Entrou no bolão com sucesso'),
            new OA\Response(response: 400, description: 'Código inválido ou bolão não encontrado'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function join(PoolJoinRequest $request): Response
    {
        $validated = $request->validated();

        try {
            $joinPool = $this->poolWriteService->joinPool($validated['join_code'], $request->user()->id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'pool' => $this->poolTransformer->item($joinPool['Pool'], 'Entrou no bolão'),
            'member' => $this->poolMemberTransformer->item($joinPool['Member'], 'Membro adicionado'),
        ], 200);
    }
}
