<?php

namespace App\Http\Controllers;

use App\Http\Requests\Leaderboard\LeaderboardTopRequest;
use App\Http\Transformers\LeaderboardTransformers\LeaderboardTransformer;
use App\Services\LeaderboardServices\LeaderboardReadService;
use App\Services\LeaderboardServices\LeaderboardWriteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class LeaderboardController extends Controller
{
    public function __construct(
        private LeaderboardReadService $readService,
        private LeaderboardWriteService $writeService,
        private LeaderboardTransformer $transformer,
    ) {}

    #[OA\Get(
        path: '/api/pools/{poolId}/leaderboard',
        summary: 'Retorna o ranking completo do bolão (paginado)',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ranking do bolão'),
            new OA\Response(response: 403, description: 'Não é membro do bolão'),
        ]
    )]
    public function index(int $poolId): Response
    {
        $paginator = $this->readService->ranking($poolId);

        return response()->json(
            $this->transformer->paginated($paginator, 'Ranking listado com sucesso'),
            200
        );
    }

    #[OA\Get(
        path: '/api/pools/{poolId}/leaderboard/top',
        summary: 'Retorna os primeiros colocados do ranking',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 3)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Top colocados'),
            new OA\Response(response: 422, description: 'Limite inválido (máximo 10)'),
        ]
    )]
    public function top(int $poolId, LeaderboardTopRequest $request): Response
    {
        $entries = $this->readService->top($poolId, $request->getLimit());

        return response()->json(
            $this->transformer->collection($entries, 'Top ranking listado com sucesso'),
            200
        );
    }

    #[OA\Get(
        path: '/api/pools/{poolId}/leaderboard/me',
        summary: 'Retorna a posição do usuário autenticado no ranking',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Posição do usuário no ranking'),
            new OA\Response(response: 404, description: 'Usuário sem entrada no ranking'),
        ]
    )]
    public function myPosition(int $poolId, Request $request): Response
    {
        $entry = $this->readService->myPosition($poolId, $request->user()->id);

        if (!$entry) {
            return response()->json(['message' => 'Você ainda não possui entrada no ranking.'], 404);
        }

        return response()->json(
            $this->transformer->item($entry, 'Sua posição no ranking'),
            200
        );
    }

    #[OA\Get(
        path: '/api/pools/{poolId}/leaderboard/{userId}',
        summary: 'Retorna as estatísticas de ranking de um participante específico',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Estatísticas do participante'),
            new OA\Response(response: 404, description: 'Participante não encontrado no ranking'),
        ]
    )]
    public function show(int $poolId, int $userId): Response
    {
        try {
            $entry = $this->readService->show($poolId, $userId);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json(
            $this->transformer->item($entry, 'Estatísticas do participante'),
            200
        );
    }

    #[OA\Post(
        path: '/api/pools/{poolId}/leaderboard/recalculate',
        summary: 'Recalcula o ranking completo do bolão a partir dos palpites',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ranking recalculado'),
            new OA\Response(response: 403, description: 'Sem permissão de administrador'),
        ]
    )]
    public function recalculate(int $poolId): Response
    {
        $this->writeService->rebuild($poolId);

        return response()->json(['message' => 'Ranking recalculado com sucesso'], 200);
    }
}
