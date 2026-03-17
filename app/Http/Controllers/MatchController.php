<?php

namespace App\Http\Controllers;

use App\Http\Requests\Match\MatchRequest;
use App\Http\Requests\Match\MatchUpdateRequest;
use App\Http\Transformers\MatchTransformers\MatchTransformer;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Match\MatchStageRequest;
use App\Repositories\MatchRepositories\MatchRepository;
use App\Services\MatchServices\MatchService;

class MatchController extends Controller
{
    private MatchRepository $matchRepository;
    private MatchService $matchService;
    private MatchTransformer $matchTransformer;

    public function __construct(MatchRepository $matchRepository, MatchService $matchService, MatchTransformer $matchTransformer)
    {
        $this->matchRepository = $matchRepository;
        $this->matchService = $matchService;
        $this->matchTransformer = $matchTransformer;
    }
    /*
    GET /api/matches                     // Lista todas as partidas
        | Critério:
        | - Retornar os dados de cada partida, incluindo os nomes dos times, o grupo, a fase do campeonato, o horário de início da partida (kickoff_at) e o placar atual.
        | Uso comum:
        | - Exibir uma lista de todas as partidas em um formato legível
        | - Permitir que os usuários vejam rapidamente os detalhes de cada partida
    */
    public function index(): Response
    {
        $matches = $this->matchRepository->findAll();
        return response()->json(new MatchTransformer()->collection($matches), 200);
    }
    /*
    GET /api/matches/{match-id}          // Mostra os detalhes de uma partida específica
                | Critério:
                | - Se a partida não for encontrada, retornar um erro 404.
                | - Retornar os dados da partida, incluindo os nomes dos times, o grupo, a fase do campeonato, o horário de início da partida (kickoff_at) e o placar atual.
                | Uso comum:
                | - Permitir que os usuários vejam os detalhes de uma partida específica
                | - Retornar um erro 404 se a partida não for encontrada
    */
    public function show($id): Response
    {
        $match = $this->matchRepository->findById($id);

        if (!$match) {
            return response()->json([
                'message' => 'Partida não encontrada',
            ], 404);
        }

        return response()->json(
            new MatchTransformer()->item($match, 'Partida encontrada'),
            200
        );
    }
    /*
    GET /api/stages/matches
            | Retorna todas as partidas de uma fase específica
            |
            | Uso comum:
            | - Listar partidas por fase da competição
            | - Mostrar jogos de oitavas, quartas, semifinal ou final
    */
    public function matchesByStage(MatchStageRequest $request)
    {
        $request->validated();

        $matches = $this->matchRepository->findByStage($request->stage);

        if ($matches->isEmpty()) {
            return response()->json([
                'message' => 'Nenhuma partida encontrada para este stage.',
            ], 404);
        }

        return $this->matchTransformer->transformMatchByStage($matches);
    }
    /*
    GET /api/group/{group-id}/matches 
            | Retorna todas as partidas de um grupo específico
            |
            | Uso comum:
            | - Listar partidas por um grupo da competição
    */
    public function matchByGroup($id): Response
    {
        $matches = $this->matchRepository->findByGroup($id);

        if ($matches === null) {
            return response()->json([
                'message' => 'Nenhuma partida encontrada para este grupo.',
            ], 404);
        }

        return response()->json(new MatchTransformer()->transformMatchByGroup($matches), 200);
    }
    /*
    POST /api/matches                     // Cria uma nova partida
            | Critério:
            | - Somente usuários com o papel de ADMIN podem criar partidas.
            | - O código do time da casa e do time visitante não podem ser iguais.
            | - Precisa informar a fase do campeonato com base no Enum MatchStage.
            | - Na fase de grupos, os times devem pertencer ao mesmo grupo.
            | - Verificar se partida já existe, ou seja, se já existe uma partida com os mesmos times e na mesma fase do campeonato.
            | - Pontuação inicial de 0 para ambos os times.
            | - O horário de início da partida (kickoff_at) é opcional, mas se fornecido, deve ser convertido para o formato de data e hora do banco de dados (Y-m-d H:i:s).
            | Uso comum:
            | - Permitir que administradores criem novas partidas
            | - Garantir que as regras de criação de partidas sejam seguidas
            | - Retornar um erro 403 se o usuário não tiver permissão para criar partidas
    */
    public function store(MatchRequest $request): Response
    {
        $request->validated();

        try {
            $match = $this->matchService->createMatch($request);
        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }

        return response()->json(
            new MatchTransformer()->item($match, 'Partida criada com sucesso'),
            201
        );
    }
    /*
    PUT /api/matches/{match}
            | Atualiza dados completos da partida
            |
            | Uso comum:
            | - Alterar data da partida
            | - Ajustar times ou fase
            | - Uso administrativo
    */
    public function update(MatchUpdateRequest $request, $id): Response
    {
        $request->validated();

        $match = $this->matchRepository->findById($id);


        if (!$match) {
            return response()->json([
                'message' => 'Partida não encontrada',
            ], 404);
        }

        try {
            $data = $this->matchService->updateMatch($request, $match);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao Atualizar a partida: ' . $e,
            ], 500);
        }

        return response()->json(
            new MatchTransformer()->item($data, 'Partida atualizada com sucesso'),
            200
        );
    }
    /*
    POST /api/matches/{match}/close
            | Fecha a partida após finalização
            |
            | Uso comum:
            | - Bloquear novos palpites
            | - Disparar cálculo de pontos dos palpites
            | - Endpoint interno/admin
    */
    public function closeMatch($id) {}
    /*
    DELETE /api/matches/{id}             // Deleta uma partida
            | Critério:
            | - Somente usuários com o papel de ADMIN podem deletar partidas.
            | - Se a partida não for encontrada, retornar um erro 404.
            | Uso comum:
            | - Permitir que administradores deletem uma partida
            | - Retornar um erro 404 se a partida não for encontrada
    */
    public function destroy($id): Response
    {
        $match = $this->matchRepository->findById($id);

        if (!$match) {
            return response()->json([
                'message' => 'Partida não encontrada',
            ], 404);
        }

        try {
            $match->delete();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar a partida',
            ], 500);
        }

        return response()->json([
            'message' => 'Partida deletada com sucesso',
        ], 200);
    }
    /*
    GET /api/matches/{match}/guesses
            | Retorna todos os palpites relacionados a uma partida
            |
            | Uso comum:
            | - Processamento interno
            | - Análise de palpites após finalização
    */
    public function guesses($id) {}
}
