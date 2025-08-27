<?php

namespace Lk\Repositories\Manager;

use App\Models\User;
use Lk\Repositories\BaseRepository;

class ManagerRepository extends BaseRepository
{

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
