<?php

namespace App\Repositories\GroupRepositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

class GroupRepository
{
    public function findAll(): Collection
    {
        return Group::query()->get();
    }

    public function findById(int $id): ?Group
    {
        return Group::query()->find($id);
    }
}
