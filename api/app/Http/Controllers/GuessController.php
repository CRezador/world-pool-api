<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuessController extends Controller
{
    /*
|--------------------------------------------------------------------------
| Guesses Endpoints
|--------------------------------------------------------------------------
|
| Representa os palpites que um usuário faz para uma partida específica
| dentro de um bolão.
|
*/

    /*
        GET /api/pools/{pool}/guesses
            | Retorna todos os palpites do usuário autenticado no bolão
            |
            | Uso comum:
            | - Tela "Meus palpites"
            | - Listar palpites já realizados
    */
    public function index($poolId, Request $request)
    {
    }
    /*
        GET /api/pools/{pool}/guesses/{match}
            | Retorna o palpite do usuário autenticado para uma partida específica
            |
            | Uso comum:
            | - Preencher tela de edição de palpite
            | - Ver palpite já registrado
    */
    public function show($poolId, $matchId, Request $request)
    {
    }
    /*
        POST /api/pools/{pool}/guesses
            | Cria um novo palpite para uma partida
            |
            | Body params:
            | - match_id
            | - home_score
            | - away_score
            |
            | Uso comum:
            | - Registrar palpite antes do início da partida
    */
    public function store(Request $request, $poolId)
    {
    }
    /*
        PUT /api/pools/{pool}/guesses/{guess}
            | Atualiza um palpite existente
            |
            | Body params:
            | - home_score
            | - away_score
            |
            | Uso comum:
            | - Alterar palpite antes do kickoff
    */
    public function update(Request $request, $poolId, $guessId)
    {
    }
    /*
        DELETE /api/pools/{pool}/guesses/{guess}
            | Remove um palpite do usuário
            |
            | Uso comum:
            | - Corrigir erro de palpite
            | - Remover palpite antes do início da partida
    */
    public function destroy($poolId, $guessId)
    {
    }
    /*
        GET /api/pools/{pool}/members/{member}/guesses
            | Retorna todos os palpites de um membro específico do bolão
            |
            | Uso comum:
            | - Ver palpites de outros participantes
            | - Comparação entre usuários
    */
    public function memberGuesses($poolId, $memberId)
    {
    }
    /*
        GET /api/pools/{pool}/matches/{match}/guesses
            | Retorna todos os palpites feitos para uma partida
            |
            | Uso comum:
            | - Ver estatísticas de palpites
            | - Comparar palpites da rodada
    */
    public function matchGuesses($poolId, $matchId)
    {
    }
    /*
        POST /api/internal/matches/{match}/guesses/score
            | Calcula os pontos de todos os palpites de uma partida
            |
            | Uso comum:
            | - Executado após o resultado da partida ser definido
            | - Atualiza o campo points da tabela guesses
    */
    public function scoreGuessesForMatch($matchId)
    {
    }
    /*
        POST /api/internal/pools/{pool}/leaderboard/recalculate
            | Recalcula todo o ranking de um bolão
            |
            | Uso comum:
            | - Após atualizar pontos dos palpites
            | - Reprocessar ranking caso necessário
    */
    public function recalculateLeaderboard($poolId)
    {
    }
    /*
        POST /api/internal/matches/{match}/process-result
            | Processa completamente o resultado da partida
            |
            | Ações executadas:
            | - Valida resultado da partida
            | - Calcula pontos dos palpites
            | - Atualiza leaderboard dos bolões afetados
            |
            | Uso comum:
            | - Fluxo automático após atualização do resultado
    */
    public function processMatchResult($matchId)
    {
    }
    /*
        POST /api/internal/guesses/{guess}/score
            | Calcula manualmente a pontuação de um palpite específico
            |
            | Uso comum:
            | - Correções administrativas
            | - Reprocessamento pontual
    */
    public function scoreGuess($guessId)
    {
    }
}
