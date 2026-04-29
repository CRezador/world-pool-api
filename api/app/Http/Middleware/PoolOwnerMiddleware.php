<?php

namespace App\Http\Middleware;

use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use App\Services\PoolMemberServices\PoolMemberService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolOwnerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $poolId = $request->route('id') ?? $request->route('poolId');

        $poolMemberService = new PoolMemberService(
            new PoolMemberRepository()
        );

        if (!$poolMemberService->isOwner((int) $poolId, $request->user()->id)) {
            return response()->json([
                'message' => 'Apenas o proprietário do bolão pode realizar esta ação.',
            ], 403);
        }

        return $next($request);
    }
}
