<?php

namespace App\Repositories\TeamRepositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository
{
    public function findAll(): Collection
    {
        return Team::with('group')->get();
    }

    public function findById(int $id): ?Team
    {
        return Team::with('group')->find($id);
    }

    public function teamsByGroup(int $id): Collection
    {
        return Team::with('group')->where('group_id', $id)->get();
    }

    public function findByCode(string $code): ?Team
    {
        return Team::query()->where('code', $code)->firstOrFail();
    }
}
