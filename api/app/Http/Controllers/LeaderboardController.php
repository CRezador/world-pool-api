<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LeaderboardController extends Controller
{
    #[OA\Get(
        path: '/api/pools/{poolId}/leaderboard',
        summary: 'Retorna o ranking completo do bolão',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ranking do bolão'),
        ]
    )]
    public function index(int $poolId) {}

    #[OA\Get(
        path: '/api/pools/{poolId}/leaderboard/top',
        summary: 'Retorna os primeiros colocados do ranking',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Top colocados'),
        ]
    )]
    public function top(int $poolId, Request $request) {}

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
        ]
    )]
    public function myPosition(int $poolId, Request $request) {}

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
        ]
    )]
    public function show(int $poolId, int $userId) {}

    #[OA\Post(
        path: '/api/pools/{poolId}/leaderboard/recalculate',
        summary: 'Recalcula o ranking após finalização de partidas',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ranking recalculado'),
        ]
    )]
    public function recalculate(int $poolId) {}

    #[OA\Post(
        path: '/api/pools/{poolId}/leaderboard/{userId}/sync',
        summary: 'Atualiza o ranking de um usuário específico no bolão (interno)',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ranking do usuário sincronizado'),
        ]
    )]
    public function syncUser(int $poolId, int $userId) {}

    #[OA\Post(
        path: '/api/pools/{poolId}/leaderboard/{userId}/create',
        summary: 'Cria entrada de ranking para um usuário ao entrar no bolão (interno)',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 201, description: 'Entrada criada'),
        ]
    )]
    public function createUser(int $poolId, int $userId) {}

    #[OA\Delete(
        path: '/api/pools/{poolId}/leaderboard/{userId}',
        summary: 'Remove o usuário do ranking do bolão (interno)',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Usuário removido do ranking'),
        ]
    )]
    public function removeUser(int $poolId, int $userId) {}

    #[OA\Post(
        path: '/api/pools/{poolId}/leaderboard/rebuild',
        summary: 'Reconstrói completamente o leaderboard com base nos palpites existentes (interno)',
        security: [['sanctum' => []]],
        tags: ['Leaderboard'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Leaderboard reconstruído'),
        ]
    )]
    public function rebuild(int $poolId) {}
}
