<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TokenController extends Controller
{
    #[OA\Post(
        path: '/api/login',
        summary: 'Autentica usuário e retorna token de acesso',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'secret'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login realizado com sucesso'),
            new OA\Response(response: 401, description: 'Credenciais inválidas'),
        ]
    )]
    public function store(LoginRequest $request): Response
    {
        $credentials = $request->validated();

        if (!Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        $request->session()->regenerate();

        return response()->json(['message' => 'Login realizado com sucesso.'], 200);
    }

    #[OA\Delete(
        path: '/api/logout',
        summary: 'Encerra a sessão do usuário autenticado',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Logout realizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout realizado com sucesso.'], 200);
    }
}
