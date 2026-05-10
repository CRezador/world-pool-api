<?php

namespace App\Http\Controllers;

use App\Http\Requests\Guess\StoreGuessRequest;
use App\Http\Requests\Guess\UpdateGuessRequest;
use App\Http\Transformers\GuessTransformers\GuessTransformer;
use App\Services\GuessServices\GuessReadService;
use App\Services\GuessServices\GuessScoringService;
use App\Services\GuessServices\GuessWriteService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class GuessController extends Controller
{
    public function __construct(
        private GuessWriteService $guessWriteService,
        private GuessReadService $guessReadService,
        private GuessScoringService $guessScoringService,
        private GuessTransformer $guessTransformer,
    ) {}

    #[OA\Get(
        path: '/api/pools/{poolId}/guesses',
        summary: 'Lista os palpites do usuário autenticado no bolão',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Palpites do usuário'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function index(Request $request, int $poolId): Response
    {
        $guesses = $this->guessReadService->getMyGuesses($poolId, $request->user()->id);

        return response()->json(
            $this->guessTransformer->collection($guesses, 'Palpites listados com sucesso'),
            200
        );
    }

    #[OA\Post(
        path: '/api/pools/{poolId}/guesses',
        summary: 'Cria um novo palpite para uma partida',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['match_id', 'home_score', 'away_score'],
                properties: [
                    new OA\Property(property: 'match_id', type: 'integer'),
                    new OA\Property(property: 'home_score', type: 'integer'),
                    new OA\Property(property: 'away_score', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Palpite criado'),
            new OA\Response(response: 400, description: 'Partida já iniciada ou dados inválidos'),
        ]
    )]
    public function store(StoreGuessRequest $request, int $poolId): Response
    {
        $data = $request->validated();

        try {
            $guess = $this->guessWriteService->createGuess([
                'user_id' => $request->user()->id,
                'pool_id' => $poolId,
                'match_id' => $data['match_id'],
                'home_score' => $data['home_score'],
                'away_score' => $data['away_score'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }

        return response()->json(
            $this->guessTransformer->item($guess, 'Palpite criado com sucesso'),
            201
        );
    }

    #[OA\Put(
        path: '/api/pools/{poolId}/guesses/{guessId}',
        summary: 'Atualiza um palpite existente',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'guessId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['home_score', 'away_score'],
                properties: [
                    new OA\Property(property: 'home_score', type: 'integer'),
                    new OA\Property(property: 'away_score', type: 'integer'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Palpite atualizado'),
            new OA\Response(response: 400, description: 'Partida já iniciada'),
        ]
    )]
    public function update(UpdateGuessRequest $request, int $poolId, int $guessId): Response
    {
        $data = $request->validated();

        try {
            $this->guessWriteService->updateGuess($guessId, $request->user()->id, $poolId, [
                'home_score' => $data['home_score'],
                'away_score' => $data['away_score'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Palpite atualizado com sucesso'], 200);
    }

    #[OA\Delete(
        path: '/api/pools/{poolId}/guesses/{guessId}',
        summary: 'Remove um palpite do usuário',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'guessId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Palpite removido'),
            new OA\Response(response: 400, description: 'Partida já iniciada'),
        ]
    )]
    public function destroy(Request $request, int $poolId, int $guessId): Response
    {
        try {
            $this->guessWriteService->deleteGuess($guessId, $request->user()->id, $poolId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Palpite removido com sucesso'], 200);
    }

    #[OA\Get(
        path: '/api/pools/{poolId}/members/{memberId}/guesses',
        summary: 'Lista todos os palpites de um membro específico no bolão',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'memberId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Palpites do membro'),
        ]
    )]
    public function memberGuesses(int $poolId, int $memberId): Response
    {
        try {
            $guesses = $this->guessReadService->getMemberGuesses($memberId, $poolId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 404);
        }

        return response()->json(
            $this->guessTransformer->collection($guesses, 'Palpites do membro listados com sucesso'),
            200
        );
    }

    #[OA\Get(
        path: '/api/pools/{poolId}/matches/{matchId}/guesses',
        summary: 'Lista todos os palpites feitos para uma partida no bolão',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'matchId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Palpites da partida'),
        ]
    )]
    public function matchGuesses(int $poolId, int $matchId): Response
    {
        try {
            $guesses = $this->guessReadService->getMatchGuesses($matchId, $poolId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 404);
        }

        return response()->json(
            $this->guessTransformer->collection($guesses, 'Palpites da partida listados com sucesso'),
            200
        );
    }

    #[OA\Post(
        path: '/api/internal/matches/{matchId}/guesses/score',
        summary: 'Calcula os pontos de todos os palpites de uma partida (interno)',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'matchId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pontos calculados'),
            new OA\Response(response: 404, description: 'Partida não encontrada'),
        ]
    )]
    public function scoreGuessesForMatch(int $matchId): Response
    {
        try {
            $this->guessScoringService->scoreGuessesForMatch($matchId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Pontuação calculada com sucesso'], 200);
    }
}
