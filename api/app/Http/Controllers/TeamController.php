<?php

namespace App\Http\Controllers;

use App\Http\Transformers\TeamTransformers\TeamTransformer;
use App\Repositories\TeamRepositories\TeamRepository;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TeamController extends Controller
{
    public function __construct(
        private TeamTransformer $teamTransformer,
        private TeamRepository $teamRepository
    ) {}

    #[OA\Get(
        path: '/api/teams',
        summary: 'Lista todas as equipes',
        security: [['sanctum' => []]],
        tags: ['Teams'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de equipes'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(): Response
    {
        $teams = $this->teamRepository->findAll();

        return response()->json($this->teamTransformer->collection($teams), 200);
    }

    #[OA\Get(
        path: '/api/teams/{id}',
        summary: 'Retorna detalhes de uma equipe específica',
        security: [['sanctum' => []]],
        tags: ['Teams'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Equipe encontrada'),
            new OA\Response(response: 404, description: 'Equipe não encontrada'),
        ]
    )]
    public function show(int $id): Response
    {
        $team = $this->teamRepository->findById($id);

        if (!$team) {
            return response()->json([
                'message' => 'Equipe não encontrada',
            ], 404);
        }

        return response()->json([
            $this->teamTransformer->item($team, 'Equipe encontrada'),
        ], 200);
    }

    #[OA\Get(
        path: '/api/groups/{id}/teams',
        summary: 'Lista todas as equipes de um grupo',
        security: [['sanctum' => []]],
        tags: ['Teams'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Equipes do grupo'),
            new OA\Response(response: 404, description: 'Grupo ou equipes não encontradas'),
        ]
    )]
    public function groupTeams(int $id): Response
    {
        $groupTeams = $this->teamRepository->teamsByGroup($id);

        if (!$groupTeams) {
            return response()->json(['message' => 'Time não encontrado'], 404);
        }

        return response()->json([
            $this->teamTransformer->transformTeamsByGroup($groupTeams),
        ], 200);
    }
}
