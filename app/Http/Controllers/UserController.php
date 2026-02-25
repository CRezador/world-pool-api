<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\CreateUserRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
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
                'updated_at' => $request->user()->updated_at
            ],
        ], 200);
    }

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
        } catch (QueryException $e) {
            // 23000 = violação de constraint (ex.: email unique)
            if (($e->errorInfo[0] ?? null) === '23000') {
                return response()->json([
                    'message' => 'Email já está em uso.',
                ], 409);
            }

            return response()->json([
                'message' => 'Erro ao criar usuário.',
            ], 500);
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
}
