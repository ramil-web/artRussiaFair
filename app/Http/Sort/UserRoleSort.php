<?php

namespace App\Http\Sort;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class UserRoleSort implements Sort
{

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        // TODO: Implement __invoke() method.
    }
}
