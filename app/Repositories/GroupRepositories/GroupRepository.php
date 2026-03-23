<?php

namespace App\Repositories\GroupRepositories;

use App\Models\Group;

class GroupRepository
{

  public function findAll()
  {
    return Group::query()->get();
  }

  public function findById($id)
  {
    return Group::query()->find($id);
  }
}
