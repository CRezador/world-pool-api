<?php

namespace App\Http\Controllers;

use App\Http\Transformers\TeamTransformers\TeamTransformer;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\TeamRepositories\TeamRepository;
use App\Services\TeamServices\TeamService;

class TeamController extends Controller
{
    public function __construct(
        private TeamTransformer $teamTransformer,
        private TeamService $teamService,
        private TeamRepository $teamRepository
    ) {
    }
    /*
        GET /api/teams                   // Retorna a lista de equipes
            | Critério:
            | - Retornar um array de equipes, cada equipe deve conter o nome, o grupo e o código da equipe
    */
    public function index(): Response
    {
        $teams = $this->teamRepository->findAll();

        return response()->json($this->teamTransformer->collection($teams), 200);
    }
    /*
        GET /api/teams/{team-id}              // Retorna os detalhes de uma equipe específica
            | Critério:
            | - Retornar o nome, o grupo e o código da equipe
            | - Retornar um erro 404 se a equipe não for encontrada
    */
    public function show($id): Response
    {
        $team = $this->teamRepository->findById($id);

        if (!$team) {
            return response()->json([
                'message' => 'Equipe não encontrada'
            ], 404);
        }

        return response()->json(
            $this->teamTransformer->item($team, 'Equipe encontrada'),
            200
        );
    }
    /*
        GET /api/groups/{group}/teams
            | Retorna todos os times que pertencem ao grupo
            |
            | Uso comum:
            | - Mostrar tabela de times do grupo
    */
    public function groupTeams($id)
    {
        $groupTeams = $this->teamRepository->teamsByGroup($id);

        if (!$groupTeams) {
            return response()->json(['message' => 'Time não encontrado'], 404);
        }

        return $this->teamTransformer->transformTeamsByGroup($groupTeams);
    }
}
