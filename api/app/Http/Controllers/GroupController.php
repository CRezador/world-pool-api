<?php

namespace App\Http\Controllers;

use App\Http\Transformers\GroupTransformers\GroupTransformer;
use App\Repositories\GroupRepositories\GroupRepository;
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

    public function __construct(
        private GroupTransformer $groupTransformer,
        private GroupRepository $groupRepository
    ) {}

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
        $group = $this->groupRepository->findAll();

        return response()->json(
            $this->groupTransformer->collection($group),
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
    public function show(int $id): Response
    {
        $group = $this->groupRepository->findById($id);

        if (!$group) {
            return response()->json([
                'message' => 'Grupo não encontrado',
            ], 404);
        }

        return response()->json(
            $this->groupTransformer->item($group, 'Grupo encontrado'),
            200
        );
    }
}
