<?php

namespace App\Http\Controllers;

use App\Services\ActivityServices\ActivityReadService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ActivityController extends Controller
{
    public function __construct(
        private ActivityReadService $readService,
    ) {}

    #[OA\Get(
        path: '/api/me/activity',
        summary: 'Retorna o feed de atividades recentes dos bolões do usuário autenticado',
        security: [['sanctum' => []]],
        tags: ['Activity'],
        responses: [
            new OA\Response(response: 200, description: 'Feed de atividades'),
        ]
    )]
    public function myActivity(Request $request): Response
    {
        $activity = $this->readService->getRecentActivity($request->user()->id);

        return response()->json([
            'message' => 'Atividade carregada com sucesso',
            'data'    => $activity,
        ], 200);
    }
}
