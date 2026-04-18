<?php

namespace App\Http\Controllers;

use App\Http\Transformers\PoolMemberTransformers\PoolMemberTransformer;
use App\Services\PoolMemberServices\PoolMemberService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolMemberController extends Controller
{
    public function __construct(
        private PoolMemberService $poolMemberService,
        private PoolMemberTransformer $poolMemberTransformer,
    ) {

    }
    /*
        GET /api/pools/{pool}/members
            | Lista todos os membros de um bolão
            |
            | Uso comum:
            | - Mostrar participantes
            | - Exibir ranking
            | - Ver quem está no bolão
    */
    public function index($poolId): Response
    {
        $members = $this->poolMemberService->listMembers($poolId);
        return response()->json([
            $this->poolMemberTransformer->collection($members, 'Lista de membros do bolão')
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
    public function show($poolId, $memberId)
    {
    }
    /*
        Private
            | Cria automaticamente o owner do bolão na tabela pool_members
            |
            | Uso comum:
            | - Executado após criação do Pool
            | - Sincroniza owner_user_id com pool_members
    */
    public function storeOwner(Request $request, $poolId)
    {
    }
    /*
     PATCH /api/pools/{pool}/members/{member}/role
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
    public function updateRole(Request $request, $poolId, $memberId)
    {
    }
    /*
        PATCH /api/pools/{pool}/members/{member}/status
            | Atualiza o status do membro no bolão
            |
            | Uso comum:
            | - Banir membro
            | - Reativar membro
    */
    public function updateStatus(Request $request, $poolId, $memberId)
    {
    }
    /*
        DELETE /api/pools/{pool}/members/{member}
            | Remove um membro do bolão
            |
            | Permissões:
            | - owner/admin pode remover qualquer membro
            | - usuário pode sair do bolão
            | - usuário fica marcado como "BANNED" em vez de ser deletado, para manter histórico
            | - Retornar um erro 403 se o usuário não tiver permissão para remover o membro
            | - Retornar um erro 404 se o bolão ou o membro não for encontrado
    */
    public function destroy($poolId, $memberId)
    {
    }
    /*
        POST /api/pools/{pool}/members/leave
            | Usuário autenticado sai do bolão
            |
            | Uso comum:
            | - Usuário decide sair do bolão
    */
    public function leave(Request $request, $poolId)
    {
    }
    /*
        POST /api/pools/{pool}/members/{member}/ban
            | Bane um membro do bolão
            |
            | Uso comum:
            | - Administração do bolão
    */
    public function ban($poolId, $memberId)
    {
    }
    /*
        POST /api/pools/{pool}/members/{member}/unban
            | Remove banimento de um membro
            |
            | Uso comum:
            | - Reabilitar participante
    */
    public function unban($poolId, $memberId)
    {
    }
}
