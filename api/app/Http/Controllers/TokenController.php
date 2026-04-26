<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Transformers\TokenTransformers\TokenTransformer;
use App\Services\UserServices\UserService;
use Symfony\Component\HttpFoundation\Response;

class TokenController extends Controller
{
    public function __construct(
        private UserService $userService,
        private TokenTransformer $tokenTransformer
    ) {}

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

        try {
            $user = $this->userService->login($credentials['email'], $credentials['password']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            $this->tokenTransformer->item($token, 'Login realizado com sucesso'),
            200
        );
    }
}
