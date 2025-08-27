<?php

namespace App\Repositories\UserApplication;

use App\Models\UserApplicationImages;
use App\Repositories\BaseRepository;

class UserApplicationImageRepository extends BaseRepository
{
    protected \Illuminate\Database\Eloquent\Model $model;

    public function __construct(UserApplicationImages $model)
    {
        $this->model = $model;
    }
}
