<?php

namespace App\Http\Controllers;

use App\Http\Requests\MatchRequest;
use App\Http\Transformers\MatchTransformer;
use App\Models\Matches;
use App\Models\Team;
use App\Http\Enums\MatchStatus;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Controller;
use App\Http\Enums\MatchStage;
use App\Http\Requests\MatchUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    use AuthorizesRequests;
    //Listar todas as partidas, criar uma nova partida e mostrar os detalhes de uma partida específica.
    /* 
      ---- Listar todas as partidas: GET /matches
            -Base no transformer MatchTransformer, retornar os dados de cada partida, incluindo os nomes dos times, o grupo, a fase do campeonato, o horário de início da partida (kickoff_at) e o placar atual.
      ---- Criar uma nova partida: POST /matches
        Regras:
            - somente usuários com o papel de ADMIN podem criar partidas.
            - O código do time da casa e do time visitante não podem ser iguais.
            - Precisa informar a fase do campeonato com base no Enum MatchStage.
            - Na fase de grupos, os times devem pertencer ao mesmo grupo.
            - Verificar se partida já existe, ou seja, se já existe uma partida com os mesmos times e na mesma fase do campeonato.
            - pontuação inicial de 0 para ambos os times.
            - O horário de início da partida (kickoff_at) é opcional, mas se fornecido, deve ser convertido para o formato de data e hora do banco de dados (Y-m-d H:i:s).
      ---- Mostrar os detalhes de uma partida específica: GET /matches/{id}
            - Se a partida não for encontrada, retornar um erro 404.
            - Base no transformer MatchTransformer, retornar os dados da partida, incluindo os nomes dos times, o grupo, a fase do campeonato, o horário de início da partida (kickoff_at) e o placar atual.
      ---- Atualizar dados de uma partida: PUT /matches/{id}/score
        Regras:
            - O placar deve ser atualizado apenas para partidas que já tenham sido criadas.
            - O placar deve ser um número inteiro não negativo.
            - kickoff_at deve ser uma data válida no formato d/m/Y.

    */
    private function kickoffFormat($kickoff_at): string|null
    {
        return $kickoff_at === null ? null : Carbon::createFromFormat('d/m/y', $kickoff_at)->format('Y-m-d H:i:s');
    }

    public function index(): Response
    {
        $matches = Matches::query()->get();

        return response()->json([
            'data' => $matches->map(function ($match) {
                return MatchTransformer::transform($match);
            })
        ], 200);
    }

    public function store(MatchRequest $request): Response
    {
        $request->validated();


        if ($request->code_home_team === $request->code_away_team) {
            return response()->json([
                'message' => 'O código do time da casa e do time visitante não podem ser iguais.',
            ], 400);
        }

        //Precisa informar a fase do campeonato com base no Enum MatchStage.
        $homeTeam = Team::query()->select(['id', 'group_id'])->where('code', strtoupper($request->code_home_team))->firstOrFail();
        $awayTeam = Team::query()->select(['id', 'group_id'])->where('code', strtoupper($request->code_away_team))->firstOrFail();

        $stageToUpper = strtoupper($request->stage);
        if ($stageToUpper === MatchStage::GROUP_STAGE->value && $homeTeam->group_id !== $awayTeam->group_id) {
            return response()->json([
                'message' => 'Na fase de grupos, os times devem pertencer ao mesmo grupo.',
            ], 400);
        }
        //Verificar se partida já existe, ou seja, se já existe uma partida com os mesmos times e na mesma fase do campeonato.
        if (Matches::query()->where('home_team_id', $homeTeam->id)
            ->where('away_team_id', $awayTeam->id)
            ->where('stage', $request->stage)
            ->exists()
        ) {
            return response()->json([
                'message' => 'Essa partida já existe.',
            ], 400);
        }

        try {
            $match = Matches::create([
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'group_id' => $stageToUpper === null ? null : $homeTeam->group_id, // Considerando que ambos os times estão no mesmo grupo
                'stage' => $stageToUpper,
                'kickoff_at' => $this->kickoffFormat($request->kickoff_at),
                'home_score' => 0,
                'away_score' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar a partida',
            ], 500);
        }

        return response()->json([
            'message' => 'Partida criada com sucesso',
            'id' => MatchTransformer::transform($match)
        ], 201);
    }


    public function show($id): Response
    {
        $match = Matches::query()->where('id', $id)->first();

        if (!$match) {
            return response()->json([
                'message' => 'Partida não encontrada',
            ], 404);
        }

        return response()->json([
            'message' => 'Partida encontrada',
            'data' => [
                MatchTransformer::transform($match)
            ]
        ], 200);
    }

    public function update(MatchUpdateRequest $request, $id): Response
    {
        $request->validated();

        $match = Matches::query()->where('id', $id)->first();

        if (!$match) {
            return response()->json([
                'message' => 'Partida não encontrada',
            ], 404);
        }

        try {
            $data = $match->update([
                'home_score' => $request->home_score,
                'away_score' => $request->away_score,
                'kickoff_at' => $this->kickoffFormat($match->kickoff_at),
                'status' => $request->status === null ? $match->status : $request->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao Atualizar a partida',
            ], 500);
        }

        return response()->json([
            'message' => 'Partida atualizada com sucesso',
            'data' => $data
        ], 200);
    }
}
