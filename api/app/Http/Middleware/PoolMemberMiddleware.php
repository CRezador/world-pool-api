<?php

namespace App\Http\Middleware;

use App\Models\Pool;
use App\Services\PoolMemberServices\PoolMemberService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolMemberMiddleware
{
    public function __construct(private PoolMemberService $poolMemberService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $poolId = (int) $request->route('poolId');

        if (!Pool::find($poolId)) {
            return response()->json(['message' => 'Bolão não encontrado'], 404);
        }

        if (!$request->user() || !$this->poolMemberService->isMember($poolId, $request->user()->id)) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return $next($request);
    }
}
