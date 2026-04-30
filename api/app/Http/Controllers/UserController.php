<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\CreateUserRequest;
use App\Http\Transformers\UserTransformers\UserTransformer;
use App\Services\UserServices\UserService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private UserTransformer $userTransformer
    ) {}

    public function me(Request $request): Response
    {
        return response()->json(
            $this->userTransformer->item($request->user(), 'Dados do usuário'),
            200
        );
    }
    /*
        POST /api/register
            | Cria um novo usuário no sistema
            |
            | Uso comum:
            | - Registro de novos usuários
            | - Endpoint público
    */
    public function store(CreateUserRequest $request): Response
    {
        $validated = $request->validated();

        try {
            $user = $this->userService->createUser($validated);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erro inesperado ao criar usuário.' . $e->getMessage(),
            ], 500);
        }

        return response()->json(
            $this->userTransformer->item($user, 'Usuário criado'),
            201
        );
    }
    /*
        PATCH /api/users/{user}
            | Atualiza os dados de um usuário específico
            |
            | Uso comum:
            | - ADMIN editar dados de usuários
    */
    public function update(Request $request, int $id)
    {
    }
    /*
        DELETE /api/users/{user}
            | Remove ou desativa um usuário do sistema
            |
            | Uso comum:
            | - Administração da plataforma
            |
            | Acesso:
            | - ADMIN
    */
    public function destroy(int $id)
    {
    }
    /*
        PATCH /api/users/{user}/role
            | Atualiza o papel do usuário no sistema
            |
            | Uso comum:
            | - Promover usuário para ADMIN
            | - Rebaixar ADMIN para USER
            |
            | Acesso:
            | - ADMIN
    */
    public function updateRole(Request $request, int $id)
    {
    }
}
