<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pool\PoolRequest;
use App\Http\Requests\Pool\PoolJoinRequest;
use App\Http\Transformers\PoolMemberTransformers\PoolMemberTransformer;
use App\Http\Transformers\PoolTransformers\PoolTransformer;
use App\Services\PoolServices\PoolService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolController extends Controller
{
    public function __construct(
        private PoolService $poolService,
        private PoolTransformer $poolTransformer,
        private PoolMemberTransformer $poolMemberTransformer
    ) {

    }
    /*
        GET    /api/pools
            | Lista os bolões disponíveis
            | Uso comum:
            | - Descobrir bolões públicos
            | - Listagem geral de bolões
    */
    public function index(): Response
    {
        return response()->json([
            $this->poolTransformer->collection($this->poolService->showPublicPools(), 'Lista de bolões públicos')
        ], 200);
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
    public function myPools(Request $request): Response
    {
        $user = $request->user();

        $pools = $this->poolService->getPoolsByUserId($user->id);

        if (!$pools) {
            return response()->json([
                'message' => 'Você não é membro de nenhum bolão.'
            ], 444);
        }

        return response()->json([
            $this->poolTransformer->collection($pools, 'Lista de bolões do usuário')
        ], 200);
    }
    /*
        POST   /api/pools                // Cria um novo bolão
            | Critério:
            | - O usuário deve estar autenticado
            | Uso comum:
            | - Permitir que os usuários criem seus próprios bolões
            | - Automaticamente adicionar o usuário como membro e Owner do bolão
            | - Garantir que o bolão seja criado com o usuário como administrador
            | - Retornar um erro 401 se o usuário não estiver autenticado
    */
    public function store(PoolRequest $request): Response
    {
        $request->validated();

        try {
            $is_public = $request->is_public;
            $name = $request->name;
            $pool = $this->poolService->createPool($is_public, $request->user(), $name);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            $this->poolTransformer->item($pool, 'Bolão criado')
        ], 201);
    }
    /*
        GET    /api/pools/{pool}         // Retorna detalhes de um bolão específico
            | Critério:
            | - O usuário deve ser membro do bolão ou o bolão deve ser público
            | Uso comum:
            | - Exibir informações detalhadas do bolão, como participantes, jogos, etc.
            | - Garantir que apenas membros ou bolões públicos sejam acessíveis
            | - Retornar um erro 404 se o bolão não for encontrado
            | - Retornar um erro 403 se o usuário não tiver permissão para acessar o bolão
    */
    public function show($id): Response
    {
        $pool = $this->poolService->showPool($id);

        if (!$pool) {
            return response()->json(['message' => 'Bolão não encontrado'], 404);
        }

        return response()->json([
            $this->poolTransformer->item($pool, 'Bolão Encontrado')
        ], 200);
    }
    /*
        DELETE /api/pools/{pool}         // Remove um bolão (owner)
            | Critério:
            | - O usuário deve ser o owner do bolão
            | Uso comum:
            | - Permitir que os proprietários de bolões excluam seus bolões
            | - Garantir que apenas o proprietário possa realizar essa ação
            | - Retornar um erro 403 se o usuário não for o proprietário do bolão
            | - Retornar um erro 404 se o bolão não for encontrado
    */
    public function destroy($id, Request $request): Response
    {

        $pool = $this->poolService->destroyPool($id, $request->user()->id);

        return response()->json([
            'Pool' => $this->poolTransformer->item($pool, 'Bolão removido')
        ], 200);
    }
    /*
        POST /api/pools/{pool}/join-code/regenerate   // Gera um novo join_code para o bolão
            | Critério:
            | - O usuário deve ser o owner do bolão
            | Uso comum:
            | - Permitir que os proprietários de bolões regenerem o código de acesso para controlar quem pode ingressar
            | - Garantir que apenas o proprietário possa realizar essa ação
            | - Retornar um erro 403 se o usuário não for o proprietário do bolão
            | - Retornar um erro 404 se o bolão não for encontrado
    */
    public function regenerateJoinCode($id, Request $request): Response
    {

        if ($request->user()->id !== $request->owner_id) {
            return response()->json([
                'message' => 'Apenas o proprietário do bolão pode regenerar o código de acesso.'
            ], 403);
        }

        try {
            $pool = $this->poolService->regenerateJoinCode($id, $request->owner_id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            $this->poolTransformer->item($pool, 'Código de acesso regenerado')
        ], 200);
    }
    /*
        PUT    /api/pools/{pool}              // Atualiza dados do bolão (nome, visibilidade, etc.)
            | Critério:
            | - O usuário deve ser o owner ou admin do bolão
            | Uso comum:
            | - Permitir que os proprietários de bolões atualizem as informações do bolão
            | - Garantir que apenas o proprietário possa realizar essa ação
            | - Retornar um erro 403 se o usuário não for o proprietário do bolão
            | - Retornar um erro 404 se o bolão não for encontrado
    */
    public function update(Request $request, $id): Response
    {
        if ($request->user()->id !== $request->owner_id) {
            return response()->json([
                'message' => 'Apenas o proprietário do bolão pode regenerar o código de acesso.'
            ], 403);
        }

        try {
            $pool = $this->poolService->updatePool($id, $request->owner_id, $request->only(['name', 'is_public']));
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            $this->poolTransformer->item($pool, 'Bolão atualizado')
        ], 200);
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
    public function join(PoolJoinRequest $request): Response
    {
        $request->validated();

        try {
            $joinPool = $this->poolService->joinPool($request->join_code, $request->user()->id);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            "Bolão" => $this->poolTransformer->item($joinPool['Pool'], 'Entrou no bolão'),
            "Membro" => $this->poolMemberTransformer->item($joinPool['Member'], 'Membro adicionado')
        ], 200);
    }
}
