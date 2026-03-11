<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends Controller
{

    public function index(): Response
    {
        $group = Group::query()->get();
        $data = $group->map(function ($group) {
            return [
                $group->name => [
                    'teams' => $group->teams->map(function ($team) {
                        return [
                            'name' => $team->name,
                            'code' => $team->code,
                        ];
                    }),
                ]
            ];
        });

        return response()->json([
            'data' => $data
        ], 200);
    }
}
