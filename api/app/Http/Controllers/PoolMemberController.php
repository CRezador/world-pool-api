<?php

namespace App\Http\Controllers;

use App\Http\Enums\PoolMemberStatus;
use App\Http\Enums\PoolUserRole;
use App\Http\Requests\PoolMember\PoolMemberUpdateRequest;
use App\Http\Transformers\PoolMemberTransformers\PoolMemberTransformer;
use App\Services\PoolMemberServices\PoolMemberService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class PoolMemberController extends Controller
{
    public function __construct(
        private PoolMemberService $poolMemberService,
        private PoolMemberTransformer $poolMemberTransformer,
    ) {}

    #[OA\Get(
        path: '/api/pools/{poolId}/members',
        summary: 'Lista membros do bolão',
        security: [['sanctum' => []]],
        tags: ['Pool Members'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'status', in: 'query', required: false, description: 'ACTIVE, BANNED ou LEFT. Não-ACTIVE requer admin.', schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Membros listados'),
            new OA\Response(response: 403, description: 'Status não-ACTIVE requer admin'),
            new OA\Response(response: 422, description: 'Status inválido'),
        ]
    )]
    public function index(Request $request, int $poolId): Response
    {
        $statusParam = $request->query('status');
        $status = null;

        if ($statusParam) {
            $status = PoolMemberStatus::tryFrom(strtoupper($statusParam));
            if (!$status) {
                return response()->json(['message' => 'Status inválido. Use: ACTIVE, BANNED ou LEFT'], 422);
            }
            if ($status !== PoolMemberStatus::ACTIVE) {
                if (!$this->poolMemberService->isAdmin($poolId, $request->user()->id)
                    && !$this->poolMemberService->isOwner($poolId, $request->user()->id)) {
                    return response()->json(['message' => 'Acesso negado'], 403);
                }
            }
        }

        $members = $this->poolMemberService->listMembers($poolId, $status);

        return response()->json(
            $this->poolMemberTransformer->collection($members, 'Membros listados com sucesso'),
            200
        );
    }

    #[OA\Get(
        path: '/api/pools/{poolId}/members/me',
        summary: 'Retorna a participação do usuário autenticado no bolão',
        security: [['sanctum' => []]],
        tags: ['Pool Members'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Participação encontrada'),
            new OA\Response(response: 404, description: 'Usuário não é membro do bolão'),
        ]
    )]
    public function me(Request $request, int $poolId): Response
    {
        try {
            $member = $this->poolMemberService->getAuthMember($poolId, $request->user()->id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(
            $this->poolMemberTransformer->item($member, 'Sua participação no bolão'),
            200
        );
    }

    #[OA\Get(
        path: '/api/pools/{poolId}/members/{memberId}',
        summary: 'Retorna detalhes de um membro específico do bolão',
        security: [['sanctum' => []]],
        tags: ['Pool Members'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'memberId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Membro encontrado'),
            new OA\Response(response: 404, description: 'Membro não encontrado'),
        ]
    )]
    public function show(int $poolId, int $memberId): Response
    {
        try {
            $member = $this->poolMemberService->getMember($poolId, $memberId);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json(
            $this->poolMemberTransformer->item($member, 'Membro encontrado'),
            200
        );
    }

    #[OA\Patch(
        path: '/api/pools/{poolId}/members/{memberId}/role',
        summary: 'Atualiza o papel de um membro no bolão (admin/owner)',
        security: [['sanctum' => []]],
        tags: ['Pool Members'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'memberId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['role'],
                properties: [
                    new OA\Property(property: 'role', type: 'string', enum: ['ADMIN', 'MEMBER']),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Papel atualizado'),
            new OA\Response(response: 403, description: 'Acesso negado'),
            new OA\Response(response: 404, description: 'Membro não encontrado'),
        ]
    )]
    public function updateRole(PoolMemberUpdateRequest $request, int $poolId, int $memberId): Response
    {
        $data = $request->validated();

        try {
            $this->poolMemberService->updateRole($poolId, $memberId, PoolUserRole::from($data['role']), $request->user()->id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }

        return response()->json([
            'message' => 'Papel do membro atualizado com sucesso',
        ], 200);
    }

    #[OA\Post(
        path: '/api/pools/{poolId}/leave',
        summary: 'Usuário autenticado sai do bolão',
        security: [['sanctum' => []]],
        tags: ['Pool Members'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Saiu do bolão'),
            new OA\Response(response: 400, description: 'Erro ao sair do bolão'),
        ]
    )]
    public function leave(Request $request, int $poolId): Response
    {
        try {
            $this->poolMemberService->leavePool($poolId, $request->user()->id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Você saiu do bolão com sucesso'], 200);
    }

    #[OA\Post(
        path: '/api/pools/{poolId}/members/{memberId}/ban',
        summary: 'Bane um membro do bolão (admin/owner)',
        security: [['sanctum' => []]],
        tags: ['Pool Members'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'memberId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Membro banido'),
            new OA\Response(response: 403, description: 'Acesso negado'),
        ]
    )]
    public function ban(Request $request, int $poolId, int $memberId): Response
    {
        try {
            $this->poolMemberService->banMember($poolId, $memberId, $request->user()->id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Membro banido com sucesso'], 200);
    }

    #[OA\Post(
        path: '/api/pools/{poolId}/members/{memberId}/unban',
        summary: 'Remove o banimento de um membro (admin/owner)',
        security: [['sanctum' => []]],
        tags: ['Pool Members'],
        parameters: [
            new OA\Parameter(name: 'poolId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'memberId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Banimento removido'),
            new OA\Response(response: 403, description: 'Acesso negado'),
        ]
    )]
    public function unban(Request $request, int $poolId, int $memberId): Response
    {
        try {
            $this->poolMemberService->unbanMember($poolId, $memberId, $request->user()->id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Membro desbanido com sucesso'], 200);
    }
}
