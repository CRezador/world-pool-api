<?php

namespace App\Repositories\TeamRepositories;

use App\Models\Team;

class TeamRepository
{
    public function findAll()
    {
        return Team::query()->get();
    }

    public function findById($id)
    {
        return Team::query()->find($id);
    }

    public function teamsByGroup($id)
    {
        return Team::select()->where('group_id')->get();
    }
}
