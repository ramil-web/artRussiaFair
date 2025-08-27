<?php

namespace Lk\Http\Sort;

use Illuminate\Database\Eloquent\Builder;

class UserRoleSort implements \Spatie\QueryBuilder\Sorts\Sort
{

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        // TODO: Implement __invoke() method.
    }
}
