<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Models\Team;


class TeamController extends Controller
{


    public function index(): Response
    {
        $teams = Team::query()->get();

        $data = $teams->map(function ($team) {
            return [
                'name' => $team->name,
                'group' => $team->group->name,
                'code' => $team->code,
            ];
        });
        return response()->json([
            'data' => $data
        ], 200);
    }
}
