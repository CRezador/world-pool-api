<?php

namespace App\Http\Controllers;

use App\Http\Transformers\TeamTransformers\TeamTransformer;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Team;


class TeamController extends Controller
{

    /*
        GET /api/teams                   // Retorna a lista de equipes
            | Critério:
            | - Retornar um array de equipes, cada equipe deve conter o nome, o grupo e o código da equipe
    */
    public function index(): Response
    {
        $teams = Team::query()->get();

        $data = $teams->map(function ($team) {
            return [
                'name' => $team->name,
                'group' => $team->group->name,
                'code' => $team->code,
            ];
        });
        return response()->json(new TeamTransformer()->collection($teams), 200);
    }
    /*
        GET /api/teams/{team-id}              // Retorna os detalhes de uma equipe específica
            | Critério:
            | - Retornar o nome, o grupo e o código da equipe
            | - Retornar um erro 404 se a equipe não for encontrada
    */
    public function show($id): Response
    {
        $team = Team::query()->find($id);

        if (!$team) {
            return response()->json([
                'message' => 'Equipe não encontrada'
            ], 404);
        }

        return response()->json(
            new TeamTransformer()->item($team, 'Equipe encontrada'),
            200
        );
    }
}
