<?php

namespace App\Http\Controllers;

use App\Http\Requests\Match\MatchRequest;
use App\Http\Requests\Match\MatchUpdateRequest;
use App\Http\Transformers\MatchTransformers\MatchTransformer;
use App\Http\Enums\MatchStatus;
use App\Http\Requests\Match\MatchStageRequest;
use App\Repositories\MatchRepositories\MatchRepository;
use App\Services\MatchServices\MatchService;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class MatchController extends Controller
{
    public function __construct(
        private MatchRepository $matchRepository,
        private MatchService $matchService,
        private MatchTransformer $matchTransformer
    ) {}

    #[OA\Get(
        path: '/api/matches',
        summary: 'Lista todas as partidas',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de partidas'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(): Response
    {
        $matches = $this->matchRepository->findAll();

        return response()->json($this->matchTransformer->collection($matches, 'Lista de partidas'), 200);
    }

    #[OA\Get(
        path: '/api/matches/{id}',
        summary: 'Retorna detalhes de uma partida específica',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Partida encontrada'),
            new OA\Response(response: 404, description: 'Partida não encontrada'),
        ]
    )]
    public function show(int $id): Response
    {
        $match = $this->matchRepository->findById($id);

        if (!$match) {
            return response()->json([
                'message' => 'Partida não encontrada',
            ], 404);
        }

        return response()->json(
            $this->matchTransformer->item($match, 'Partida encontrada'),
            200
        );
    }

    #[OA\Get(
        path: '/api/stages/matches',
        summary: 'Lista partidas de uma fase específica',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'stage', in: 'query', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Partidas da fase'),
            new OA\Response(response: 404, description: 'Nenhuma partida encontrada para o stage'),
        ]
    )]
    public function matchesByStage(MatchStageRequest $request): Response
    {
        $validated = $request->validated();

        $matches = $this->matchRepository->findByStage($validated['stage']);

        if ($matches->isEmpty()) {
            return response()->json([
                'message' => 'Nenhuma partida encontrada para este stage.',
            ], 404);
        }

        return response()->json([$this->matchTransformer->transformMatchByStage($matches)], 200);
    }

    #[OA\Get(
        path: '/api/group/{id}/matches',
        summary: 'Lista partidas de um grupo específico',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Partidas do grupo'),
            new OA\Response(response: 404, description: 'Nenhuma partida encontrada para o grupo'),
        ]
    )]
    public function matchByGroup(int $id): Response
    {
        $matches = $this->matchRepository->findByGroup($id);

        if ($matches->isEmpty()) {
            return response()->json([
                'message' => 'Nenhuma partida encontrada para este grupo.',
            ], 404);
        }

        return response()->json($this->matchTransformer->transformMatchByGroup($matches), 200);
    }

    #[OA\Post(
        path: '/api/matches/create',
        summary: 'Cria uma nova partida (admin)',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['code_home_team', 'code_away_team', 'stage', 'game_day'],
                properties: [
                    new OA\Property(property: 'game_day', type: 'integer'),
                    new OA\Property(property: 'code_home_team', type: 'string'),
                    new OA\Property(property: 'code_away_team', type: 'string'),
                    new OA\Property(property: 'home_score', type: 'integer', default: 0),
                    new OA\Property(property: 'away_score', type: 'integer', default: 0),
                    new OA\Property(property: 'kickoff_at', type: 'string', format: 'date-time', nullable: true),
                    new OA\Property(property: 'stage', type: 'string'),
                    new OA\Property(property: 'status', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Partida criada'),
            new OA\Response(response: 400, description: 'Dados inválidos'),
            new OA\Response(response: 403, description: 'Acesso negado'),
        ]
    )]
    public function store(MatchRequest $request): Response
    {
        $data = $request->validated();
        $match = [
            'game_day' => $data->game_day,
            'code_home_team' => $data->code_home_team,
            'code_away_team' => $data->code_away_team,
            'home_score' => $data->home_score ?? 0,
            'away_score' => $data->away_score ?? 0,
            'kickoff_at' => $data->kickoff_at,
            'stage' => $data->stage,
            'status' => $data->status ?? MatchStatus::SCHEDULED,
        ];

        try {
            $matchCreated = $this->matchService->createMatch($match);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }

        return response()->json(
            $this->matchTransformer->item($matchCreated, 'Partida criada com sucesso'),
            201
        );
    }

    #[OA\Put(
        path: '/api/matches/{id}',
        summary: 'Atualiza dados completos de uma partida (admin)',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Partida atualizada'),
            new OA\Response(response: 404, description: 'Partida não encontrada'),
            new OA\Response(response: 500, description: 'Erro ao atualizar'),
        ]
    )]
    public function update(MatchUpdateRequest $request, int $id): Response
    {
        $request->validated();

        $match = $this->matchRepository->findById($id);


        if (!$match) {
            return response()->json([
                'message' => 'Partida não encontrada',
            ], 404);
        }

        try {
            $data = $this->matchService->updateMatch($request, $match);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar a partida: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json(
            $this->matchTransformer->item($data, 'Partida atualizada com sucesso'),
            200
        );
    }

    #[OA\Post(
        path: '/api/matches/{id}/close',
        summary: 'Fecha a partida após finalização, bloqueando novos palpites (admin)',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Partida fechada'),
        ]
    )]
    public function closeMatch(int $id)
    {
        //@todo checar se realmente preciso dessa function
    }

    #[OA\Delete(
        path: '/api/matches/{id}',
        summary: 'Remove uma partida (admin)',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Partida deletada'),
            new OA\Response(response: 404, description: 'Partida não encontrada'),
            new OA\Response(response: 500, description: 'Erro ao deletar'),
        ]
    )]
    public function destroy(int $id): Response
    {
        $match = $this->matchRepository->findById($id);

        if (!$match) {
            return response()->json([
                'message' => 'Partida não encontrada',
            ], 404);
        }

        try {
            $this->matchRepository->delete($match);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Partida deletada com sucesso',
        ], 200);
    }

    #[OA\Get(
        path: '/api/matches/{id}/guesses',
        summary: 'Retorna todos os palpites de uma partida',
        security: [['sanctum' => []]],
        tags: ['Matches'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Palpites da partida'),
        ]
    )]
    public function guesses(int $id)
    {
        //@todo implementar função para retornar os palpites relacionados a uma partida
    }
}
