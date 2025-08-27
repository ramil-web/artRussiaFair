<?php


namespace App\Repositories\User;


use App\Models\User;
use App\Repositories\BaseRepository;


class UserRepository extends BaseRepository
{
    protected \Illuminate\Database\Eloquent\Model $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
