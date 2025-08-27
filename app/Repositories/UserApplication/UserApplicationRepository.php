<?php

namespace App\Repositories\UserApplication;

use App\Models\UserApplication;
use App\Repositories\BaseRepository;

class UserApplicationRepository extends BaseRepository
{
    protected \Illuminate\Database\Eloquent\Model $model;

    public function __construct(UserApplication $model)
    {
        $this->model = $model;
    }
}
