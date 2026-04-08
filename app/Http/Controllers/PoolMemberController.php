<?php

namespace App\Http\Controllers;

use App\Http\Requests\PoolMember\PoolMemberJoinRequest;
use App\Http\Transformers\PoolTransformers\PoolTransformer;
use App\Services\PoolMemberServices\PoolMemberService;
use Illuminate\Http\Request;

class PoolMemberController extends Controller
{
    public function __construct(
        private PoolMemberService $poolMemberService,
        private PoolTransformer $poolTransformer
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
    public function index($poolId)
    {
        $members = $this->poolMemberService->listMembers($poolId);
        return response()->json($this->poolTransformer->collection($members, 'Lista de membros do bolão'));
    }

    /*
        GET    /api/me/pools             // Lista os bolões do usuário autenticado
            | Critério:
            | - O usuário deve estar autenticado
            | Uso comum:
            | - Exibir uma lista de bolões dos quais o usuário é membro
            | - Permitir que os usuários vejam facilmente seus bolões ativos
                | - Retornar um erro 401 se o usuário não estiver autenticado
                | - Retornar um erro 444 se o usuário não for membro de nenhum bolão
    */
    public function myPools(Request $request)
    {
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
        POST /api/pools/{pool}/members/join
            | Usuário entra em um bolão usando join_code
            |
            | Body:
            | - join_code
            |
            | Uso comum:
            | - Fluxo de convite
            | - Entrar em bolão compartilhado
            | - Validar código de acesso
            | - Retornar um erro 404 se o bolão não for encontrado
            | - Retornar um erro 400 se o código de acesso for inválido
    */
    public function join(PoolMemberJoinRequest $request)
    {
        $request->validated();

        try {
            $pool = $this->poolMemberService->joinPool($request->join_code, $request->user());
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

        return $this->poolTransformer->item($pool, 'Entrou no bolão');
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
