<?php

namespace Lk\Repositories\Manager;

use App\Models\ManagerProfile;
use Lk\Repositories\BaseRepository;

class ManagerProfileRepository extends BaseRepository
{

    public function __construct(ManagerProfile $model)
    {
        $this->model = $model;
    }
}
