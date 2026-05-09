<?php

namespace App\Http\Middleware;

use App\Models\Pool;
use App\Services\PoolMemberServices\PoolMemberService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolMemberAdminMiddleware
{
    public function __construct(private PoolMemberService $poolMemberService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $poolId = (int) $request->route('poolId');
        $userId = $request->user()->id;

        if (!Pool::find($poolId)) {
            return response()->json(['message' => 'Bolão não encontrado'], 404);
        }

        if (!$this->poolMemberService->isAdmin($poolId, $userId) && !$this->poolMemberService->isOwner($poolId, $userId)) {
            return response()->json([
                'message' => 'Acesso negado',
            ], 403);
        }

        $memberId = $request->route('memberId');
        if ($memberId && !$this->poolMemberService->isOwner($poolId, $userId)) {
            if ($this->poolMemberService->isOwnerByMemberId($poolId, (int) $memberId)) {
                return response()->json([
                    'message' => 'Admins não podem alterar dados do proprietário do bolão.',
                ], 403);
            }
        }

        return $next($request);
    }
}
