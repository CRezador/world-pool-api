<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoolController extends Controller
{
    /*
        GET    /api/pools?is_public=true
            | Lista os bolões disponíveis
            |
            | Query params:
            | - search (opcional)
            | - is_public (opcional)
            |
            | Uso comum:
            | - Descobrir bolões públicos
            | - Listagem geral de bolões
    */
    public function index(Request $request) {}
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
    public function store(Request $request) {}
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
    public function show($id) {}
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
    public function myPools(Request $request) {}
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
    public function destroy($id) {}
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
