<?php

namespace App\Http\Controllers;

use App\Http\Enums\PoolUserRole;
use App\Http\Requests\PoolMember\PoolMemberUpdateRequest;
use App\Http\Transformers\PoolMemberTransformers\PoolMemberTransformer;
use App\Services\PoolMemberServices\PoolMemberService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolMemberController extends Controller
{
    public function __construct(
        private PoolMemberService $poolMemberService,
        private PoolMemberTransformer $poolMemberTransformer,
    ) {}
    /*
        GET /api/pools/{pool}/members
            | Lista todos os membros de um bolão
            |
            | Uso comum:
            | - Mostrar participantes
            | - Exibir ranking
            | - Ver quem está no bolão
    */
    public function index(int $poolId): Response
    {
        $members = $this->poolMemberService->listMembers($poolId);

        return response()->json([
            $this->poolMemberTransformer->collection($members, 'Membros listados com sucesso'),
        ], 200);
    }
    /*
        GET /api/pools/{pool}/members/{member}
            | Retorna detalhes de um membro específico do bolão
            |
            | Uso comum:
            | - Ver dados de participação
            | - Ver papel do usuário (admin/member)
    */
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
    /*
     PATCH /api/pools/{pool}/members/role
        | Atualiza o papel de um membro no bolão (admin/owner)
        |
        | Body:
        | - role (admin/member)
        |
        | Uso comum:
        | - Gerenciar permissões
        | - Promover/demover membros
        | - Controlar acesso a funcionalidades do bolão
        | - Não é possível alterar o papel do próprio usuário
        | - Retornar um erro 403 se o usuário não tiver permissão para alterar o papel
        | - Retornar um erro 404 se o bolão ou o membro não for encontrado
    */
    public function updateRole(PoolMemberUpdateRequest $request, int $poolId): Response
    {
        $data = $request->validated();

        try {
            $this->poolMemberService->updateRole($poolId, $data['user_id'], PoolUserRole::from($data['role']));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }

        return response()->json([
            'message' => 'Papel do membro atualizado com sucesso',
        ], 200);
    }
    /*
        POST /api/pools/{pool}/leave
            | Usuário autenticado sai do bolão
            |
            | Uso comum:
            | - Usuário decide sair do bolão
    */
    public function leave(Request $request, int $poolId): Response
    {
        try {
            $this->poolMemberService->leavePool($poolId, $request->user()->id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Você saiu do bolão com sucesso'], 200);
    }
    /*
        POST /api/pools/{pool}/members/{member}/ban
            | Bane um membro do bolão
            |
            | Uso comum:
            | - Administração do bolão
    */
    public function ban(Request $request, int $poolId, int $memberId): Response
    {
        try {
            $this->poolMemberService->banMember($poolId, $memberId, $request->user()->id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 400);
        }

        return response()->json(['message' => 'Membro banido com sucesso'], 200);
    }
    /*
        POST /api/pools/{pool}/members/{member}/unban
            | Remove banimento de um membro
            |
            | Uso comum:
            | - Reabilitar participante
    */
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
