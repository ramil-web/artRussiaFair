<?php

namespace Admin\Repositories\UserApplication;

use App\Models\UserApplicationImages;
use Admin\Repositories\BaseRepository;

class UserApplicationImageRepository extends BaseRepository
{

    public function __construct(UserApplicationImages $model)
    {
        $this->model = $model;
    }
}
