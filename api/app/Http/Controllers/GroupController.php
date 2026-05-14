<?php

namespace App\Http\Controllers;

use App\Http\Transformers\GroupTransformers\GroupTransformer;
use App\Services\GroupServices\GroupReadService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends Controller
{
    public function __construct(
        private GroupTransformer $groupTransformer,
        private GroupReadService $groupReadService
    ) {}

    #[OA\Get(
        path: '/api/groups',
        summary: 'Lista todos os grupos da copa',
        security: [['sanctum' => []]],
        tags: ['Groups'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de grupos'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(): Response
    {
        return response()->json(
            $this->groupTransformer->collection($this->groupReadService->listGroups(), 'Lista de grupos'),
            200
        );
    }

    #[OA\Get(
        path: '/api/groups/{id}',
        summary: 'Retorna detalhes de um grupo específico',
        security: [['sanctum' => []]],
        tags: ['Groups'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Grupo encontrado'),
            new OA\Response(response: 404, description: 'Grupo não encontrado'),
        ]
    )]
    public function show(int $id): Response
    {
        $group = $this->groupReadService->getGroup($id);

        if (!$group) {
            return response()->json(['message' => 'Grupo não encontrado'], 404);
        }

        return response()->json(
            $this->groupTransformer->item($group, 'Grupo encontrado'),
            200
        );
    }
}
