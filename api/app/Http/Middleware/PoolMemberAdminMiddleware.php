<?php

namespace App\Http\Middleware;

use App\Repositories\PoolMemberRepositories\PoolMemberRepository;
use App\Services\PoolMemberServices\PoolMemberService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoolMemberAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $poolId = (int) $request->route('poolId');
        $userId = $request->user()->id;

        $poolMemberService = new PoolMemberService(
            new PoolMemberRepository()
        );

        if (!$poolMemberService->isAdmin($poolId, $userId) && !$poolMemberService->isOwner($poolId, $userId)) {
            return response()->json([
                'message' => 'Acesso negado',
            ], 403);
        }

        $memberId = $request->route('memberId');
        if ($memberId && !$poolMemberService->isOwner($poolId, $userId)) {
            if ($poolMemberService->isOwnerByMemberId($poolId, (int) $memberId)) {
                return response()->json([
                    'message' => 'Admins não podem alterar dados do proprietário do bolão.',
                ], 403);
            }
        }

        return $next($request);
    }
}
