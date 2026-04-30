<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /*
|--------------------------------------------------------------------------
| Leaderboard Endpoints
|--------------------------------------------------------------------------
|
| Representa a classificação dos participantes dentro de um bolão
| com base nos pontos acumulados pelos palpites.
|
*/

    /*
        GET /api/pools/{pool}/leaderboard
            | Retorna o ranking completo do bolão
            |
            | Uso comum:
            | - Tela principal de classificação
            | - Listar todos os participantes ordenados por pontos
    */
    public function index(int $poolId)
    {
    }
    /*
        GET /api/pools/{pool}/leaderboard/top
            | Retorna os primeiros colocados do ranking
            |
            | Query params:
            | - limit (opcional)
            |
            | Uso comum:
            | - Mostrar top 3 ou top 10 do bolão
    */
    public function top(int $poolId, Request $request)
    {
    }
    /*
        GET /api/pools/{pool}/leaderboard/me
            | Retorna a posição do usuário autenticado no ranking
            |
            | Uso comum:
            | - Mostrar posição pessoal no ranking
            | - Destacar desempenho do usuário
    */
    public function myPosition(int $poolId, Request $request)
    {
    }
    /*
        GET /api/pools/{pool}/leaderboard/{user}
            | Retorna as estatísticas de ranking de um participante específico
            |
            | Uso comum:
            | - Ver pontuação detalhada de um participante
            | - Comparar desempenho entre membros
    */
    public function show(int $poolId, int $userId)
    {
    }
    /*
        POST /api/pools/{pool}/leaderboard/recalculate
            | Recalcula e atualiza o ranking do bolão após finalização de partidas
            |
            | Uso comum:
            | - Atualizar pontuação após fechamento de uma rodada
            | - Processar pontos dos palpites com base nos resultados das partidas
    */
    public function recalculate(int $poolId)
    {
    }
    /*
        POST /api/pools/{pool}/leaderboard/{user}/sync
            | Atualiza o ranking de um usuário específico no bolão
            |
            | Uso comum:
            | - Atualizar pontos após registrar ou corrigir um palpite
            |
            | Interno:
            | - Usado por serviços internos ou jobs
    */
    public function syncUser(int $poolId, int $userId)
    {
    }
    /*
        POST /api/pools/{pool}/leaderboard/{user}/create
            | Cria a entrada inicial de ranking para um usuário ao entrar no bolão
            |
            | Uso comum:
            | - Executado quando usuário entra em um bolão
            |
            | Interno:
            | - Chamado pelo PoolMemberController
    */
    public function createUser(int $poolId, int $userId)
    {
    }
    /*
        DELETE /api/pools/{pool}/leaderboard/{user}
            | Remove o usuário do ranking do bolão
            |
            | Uso comum:
            | - Quando o usuário sai ou é removido do bolão
            |
            | Interno:
            | - Chamado pelo PoolMemberController
    */
    public function removeUser(int $poolId, int $userId)
    {
    }
    /*
        POST /api/pools/{pool}/leaderboard/rebuild
            | Reconstrói completamente o leaderboard com base nos palpites existentes
            |
            | Uso comum:
            | - Correção de inconsistências
            | - Reprocessamento completo do ranking
            |
            | Interno:
            | - Ferramenta administrativa ou manutenção
    */
}
