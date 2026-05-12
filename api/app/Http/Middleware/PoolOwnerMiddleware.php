<?php

namespace App\Http\Middleware;

use App\Services\PoolMemberServices\PoolMemberReadService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolOwnerMiddleware
{
    public function __construct(private PoolMemberReadService $poolMemberReadService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $poolId = $request->route('id') ?? $request->route('poolId');

        if (!$this->poolMemberReadService->isOwner((int) $poolId, $request->user()->id)) {
            return response()->json([
                'message' => 'Apenas o proprietário do bolão pode realizar esta ação.',
            ], 403);
        }

        return $next($request);
    }
}
