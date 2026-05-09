<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class GuessController extends Controller
{
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
    public function index(int $poolId, Request $request) {}

    #[OA\Get(
        path: '/api/pools/{poolId}/guesses/{matchId}',
        summary: 'Retorna o palpite do usuário autenticado para uma partida específica',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'matchId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Palpite encontrado'),
            new OA\Response(response: 404, description: 'Palpite não encontrado'),
        ]
    )]
    public function show(int $poolId, int $matchId, Request $request) {}

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
    public function store(Request $request, int $poolId) {}

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
    public function update(Request $request, int $poolId, int $guessId) {}

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
    public function destroy(int $poolId, int $guessId) {}

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
    public function memberGuesses(int $poolId, int $memberId) {}

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
    public function matchGuesses(int $poolId, int $matchId) {}

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
        ]
    )]
    public function scoreGuessesForMatch(int $matchId) {}

    #[OA\Post(
        path: '/api/internal/pools/{poolId}/leaderboard/recalculate',
        summary: 'Recalcula o ranking de um bolão (interno)',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ranking recalculado'),
        ]
    )]
    public function recalculateLeaderboard(int $poolId) {}

    #[OA\Post(
        path: '/api/internal/matches/{matchId}/process-result',
        summary: 'Processa completamente o resultado de uma partida (interno)',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'matchId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Resultado processado'),
        ]
    )]
    public function processMatchResult(int $matchId) {}

    #[OA\Post(
        path: '/api/internal/guesses/{guessId}/score',
        summary: 'Calcula a pontuação de um palpite específico (interno)',
        security: [['sanctum' => []]],
        tags: ['Guesses'],
        parameters: [
            new OA\Parameter(name: 'guessId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pontuação calculada'),
        ]
    )]
    public function scoreGuess(int $guessId) {}
}
