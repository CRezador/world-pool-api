<?php

namespace App\Http\Controllers;

use App\Http\Transformers\GroupTransformers\GroupTransformer;
use App\Models\Group;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends Controller
{
    /*
    ----Listar todas as equipes agrupadas por grupo: GET /groups
        -Retornar os grupos, e para cada grupo, retornar as equipes que pertencem a ele. Cada equipe deve incluir seu nome e código.
    ----Listas um grupo: GET /groups/{id}
        -Retornar o grupo específico, e para ele, retornar as equipes que pertencem a ele. Cada equipe deve incluir seu nome e código.
    */
    public function index(): Response
    {
        $group = Group::query()->get();
        $data = $group->map(function ($group) {
            return [
                GroupTransformer::transform($group)
            ];
        });

        return response()->json([
            'data' => $data
        ], 200);
    }

    public function show($id): Response
    {
        $group = Group::query()->find($id);

        if (!$group) {
            return response()->json([
                'message' => 'Grupo não encontrado'
            ], 404);
        }

        return response()->json([
            'data' => GroupTransformer::transform($group)
        ], 200);
    }
}
