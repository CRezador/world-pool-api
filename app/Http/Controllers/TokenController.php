<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\Authentication\LoginRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

class TokenController extends Controller
{
    /*
        POST /api/login
            | Autentica um usuário e retorna um token de acesso
            |
            | Uso comum:
            | - Login de usuários existentes
            | - Endpoint público
    */
    public function store(LoginRequest $request): Response
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();


        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
}
