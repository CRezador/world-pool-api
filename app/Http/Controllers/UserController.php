<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    public function me(Request $request): Response
    {
        return response()->json([
            'data' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'created_at' => $request->user()->created_at,
                'updated_at' => $request->user()->updated_at,
            ],
        ], 200);
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
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'password' => Hash::make($validated['password']),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Erro inesperado ao criar usuário.' . $e,
            ], 500);
        }


        return response()->json([
            'message' => "User Created",
            'data' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ],
        ], 201);
    }
    /*
        PATCH /api/users/{user}
            | Atualiza os dados de um usuário específico
            |
            | Uso comum:
            | - ADMIN editar dados de usuários
    */
    public function update(Request $request, $id): void {}
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
    public function destroy($id): void {}
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
    public function updateRole(Request $request, $id): void {}
}
