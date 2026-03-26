<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Pool\PoolRequest;
use App\Http\Transformers\PoolTransformers\PoolTransformer;
use App\Repositories\PoolRepositories\PoolRepository;
use App\Services\PoolServices\PoolService;
use Illuminate\Http\Request; 

class PoolController extends Controller
{

    public function __construct(
        private PoolRepository $poolRepository,
        private PoolService $poolService,
        private PoolTransformer $poolTransformer
    )
    {
     
    }
    /*
        GET    /api/pools
            | Lista os bolões disponíveis
            | Uso comum:
            | - Descobrir bolões públicos
            | - Listagem geral de bolões
    */
    public function index() {
        return $this->poolTransformer->collection($this->poolService->showPublicPools());
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
    public function store(PoolRequest $request) {
        $request->validated();

        try{
            $is_public = $request->is_public;
            $pool = $this->poolService->createPool($is_public, $request->user());
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

        return $this->poolTransformer->item($pool, 'Bolão criado');
                
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
    public function show($id) {
        $pool = $this->poolService->showPool($id);

        if(!$pool){
            return response()->json(['message' => 'Bolão não encontrado'],404);
        }

        return $this->poolTransformer->item($pool, 'Bolão Encontrado');
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
    public function destroy($id, Request $request) {
        
    $pool = $this->poolService->destroyPool($id, $request->user()->id);

    return response()->json([
        'Pool' => $this->poolTransformer->item($pool, 'Bolão removido') 
    ], 200);
    }
    /*
        POST /api/pools/join
            | Entrar em um bolão através do código
            |
            | Body:
            | - join_code
            |
            | Uso comum:
            | - Usuário entra em um bolão privado ou público
    */
    public function join(Request $request) {}
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
    public function regenerateJoinCode($id) {}
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
    public function update(Request $request, $id) {}
}
