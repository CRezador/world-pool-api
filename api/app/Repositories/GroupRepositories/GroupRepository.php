<?php

namespace App\Repositories\GroupRepositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository
{
    public function findAll(): Collection
    {
        return Group::with('teams')->get();
    }

    public function findById(int $id): ?Group
    {
        return Group::with('teams')->find($id);
    }
}
