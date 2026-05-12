<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Transformers\TokenTransformers\TokenTransformer;
use App\Services\TokenServices\TokenWriteService;
use App\Services\UserServices\UserWriteService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TokenController extends Controller
{
    public function __construct(
        private UserWriteService $userWriteService,
        private TokenWriteService $tokenWriteService,
        private TokenTransformer $tokenTransformer
    ) {}

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
            new OA\Response(response: 200, description: 'Token gerado com sucesso'),
            new OA\Response(response: 401, description: 'Credenciais inválidas'),
        ]
    )]
    public function store(LoginRequest $request): Response
    {
        $credentials = $request->validated();

        try {
            $user = $this->userWriteService->login($credentials['email'], $credentials['password']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 500);
        }

        $token = $this->tokenWriteService->createToken($user);

        return response()->json(
            $this->tokenTransformer->item($token, 'Login realizado com sucesso'),
            200
        );
    }

    #[OA\Delete(
        path: '/api/logout',
        summary: 'Revoga o token de acesso do usuário autenticado',
        security: [['sanctum' => []]],
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Logout realizado com sucesso'),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function destroy(Request $request): Response
    {
        $this->tokenWriteService->revokeCurrentToken($request->user());

        return response()->json(['message' => 'Logout realizado com sucesso'], 200);
    }
}
