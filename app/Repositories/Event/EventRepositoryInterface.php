<?php

namespace App\Repositories\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

interface EventRepositoryInterface

{
    public function get(int $id): Collection|Model|QueryBuilder|array;
}
