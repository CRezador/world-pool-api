<?php

namespace App\Http\Controllers;

use App\Http\Requests\MatchRequest;
use App\Models\Matches;
use App\Models\Team;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class MatchController extends Controller
{
    public function index(): Response
    {
        $matches = Matches::query()->get();

        $data = $matches->map(function ($match) {
            return [
                'home_team' => $match->homeTeam->name,
                'away_team' => $match->awayTeam->name,
                'stage' => $match->stage,
                'group' => $match->stage->name === 'GROUP_STAGE'
                    ? $match->group->name
                    : 'Fase Eliminatória',
                'kickoff_at' => $match->kickoff_at === null ? "A ser definido" : $match->kickoff_at->format('d/m/Y'),
                'home_score' => $match->home_score,
                'away_score' => $match->away_score,
            ];
        });

        return response()->json([
            'data' => $data
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
        if ($stageToUpper === 'GROUP' && $homeTeam->group_id !== $awayTeam->group_id) {
            return response()->json([
                'message' => 'Na fase de grupos, os times devem pertencer ao mesmo grupo.',
            ], 400);
        }

        try {
            $match = Matches::create([
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'group_id' => $stageToUpper === null ? null : $homeTeam->group_id, // Considerando que ambos os times estão no mesmo grupo
                'stage' => $stageToUpper,
                'kickoff_at' => $request->kickoff_at === null ? null : Carbon::createFromFormat('d/m/y', $request->kickoff_at)->startOfDay()->format('Y-m-d H:i:s'),
                'home_score' => 0,
                'away_score' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar a partida: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Partida criada com sucesso',
            'id' => $match
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
                'home_team' => $match->homeTeam->name,
                'away_team' => $match->awayTeam->name,
                'stage' => $match->stage,
                'group' => $match->stage->name === 'GROUP_STAGE'
                    ? $match->group->name
                    : 'Fase Eliminatória',
                'kickoff_at' => $match->kickoff_at === null ? "A ser definido" : $match->kickoff_at->format('d/m/Y'),
                'home_score' => $match->home_score,
                'away_score' => $match->away_score,
            ]
        ], 200);
    }
}
