<?php

namespace App\Http\Controllers;

use App\Http\Enums\UserRole;
use App\Http\Requests\Authentication\CreateUserRequest;
use App\Http\Requests\Authentication\UpdateUserRequest;
use App\Http\Requests\Authentication\UpdateUserRoleRequest;
use App\Http\Transformers\UserTransformers\UserTransformer;
use App\Services\UserServices\UserService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private UserTransformer $userTransformer
    ) {}

    #[OA\Get(
        path: '/api/me',
        summary: 'Retorna dados do usuário autenticado',
        security: [['sanctum' => []]],
        tags: ['Users'],
        responses: [
            new OA\Response(response: 200, description: 'Dados do usuário'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function me(Request $request): Response
    {
        return response()->json(
            $this->userTransformer->item($request->user(), 'Dados do usuário'),
            200
        );
    }

    #[OA\Post(
        path: '/api/register',
        summary: 'Cria um novo usuário',
        tags: ['Users'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'João Silva'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Usuário criado'),
            new OA\Response(response: 422, description: 'Dados inválidos'),
            new OA\Response(response: 500, description: 'Erro inesperado'),
        ]
    )]
    public function store(CreateUserRequest $request): Response
    {
        $validated = $request->validated();

        try {
            $user = $this->userService->createUser($validated);
        } catch (Throwable) {
            return response()->json([
                'message' => 'Erro inesperado ao criar usuário.',
            ], 500);
        }

        return response()->json(
            $this->userTransformer->item($user, 'Usuário criado'),
            201
        );
    }

    #[OA\Patch(
        path: '/api/users/{id}',
        summary: 'Atualiza dados de um usuário (admin)',
        security: [['sanctum' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Usuário atualizado'),
            new OA\Response(response: 403, description: 'Acesso negado'),
            new OA\Response(response: 404, description: 'Usuário não encontrado'),
        ]
    )]
    public function update(UpdateUserRequest $request, int $id): Response
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $user = $this->userService->update($user, $request->validated());

        return response()->json(
            $this->userTransformer->item($user, 'Usuário atualizado'),
            200
        );
    }

    #[OA\Patch(
        path: '/api/users/{id}/role',
        summary: 'Atualiza o papel do usuário no sistema (admin)',
        security: [['sanctum' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['role'],
                properties: [
                    new OA\Property(property: 'role', type: 'string', enum: ['USER', 'ADMIN']),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Papel atualizado'),
            new OA\Response(response: 403, description: 'Acesso negado'),
        ]
    )]
    public function updateRole(UpdateUserRoleRequest $request, int $id): Response
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $role = UserRole::from($request->validated()['role']);
        $user = $this->userService->updateRole($user, $role);

        return response()->json(
            $this->userTransformer->item($user, 'Papel atualizado'),
            200
        );
    }
}
