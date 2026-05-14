<?php

namespace App\Services\GroupServices;

use App\Models\Group;
use App\Repositories\GroupRepositories\GroupRepository;
use Illuminate\Database\Eloquent\Collection;

class GroupReadService
{
    public function __construct(private GroupRepository $groupRepository) {}

    public function listGroups(): Collection
    {
        return $this->groupRepository->findAll();
    }

    public function getGroup(int $id): ?Group
    {
        return $this->groupRepository->findById($id);
    }
}
