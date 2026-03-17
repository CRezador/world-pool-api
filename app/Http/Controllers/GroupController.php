<?php

namespace App\Http\Controllers;

use App\Http\Transformers\GroupTransformers\GroupTransformer;
use App\Models\Group;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends Controller
{
    /*
|--------------------------------------------------------------------------
| Groups Endpoints
|--------------------------------------------------------------------------
|
| Representa os grupos da copa (A, B, C...) e organiza os times
| que pertencem a cada grupo.
|
*/

    /*
        GET /api/groups
            | Retorna todos os grupos cadastrados
            |
            | Uso comum:
            | - Listar todos os grupos da copa
            | - Tela inicial de grupos
    */
    public function index(): Response
    {
        $group = Group::query()->get();

        return response()->json(
            new GroupTransformer()->collection($group),
            200
        );
    }
    /*
        GET /api/groups/{group}
            | Retorna informações de um grupo específico
            |
            | Uso comum:
            | - Visualizar detalhes de um grupo
            | - Mostrar dados do grupo em tela
    */
    public function show($id): Response
    {
        $group = Group::query()->find($id);

        if (!$group) {
            return response()->json([
                'message' => 'Grupo não encontrado'
            ], 404);
        }

        return response()->json(
            new GroupTransformer()->item($group, 'Grupo encontrado'),
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
    public function teams($id) {}
    /*
        GET /api/groups/{group}/matches
            | Retorna todas as partidas do grupo
            |
            | Uso comum:
            | - Listar jogos da fase de grupos
            | - Tela de calendário de partidas do grupo
    */
    public function matches($id) {}
}
