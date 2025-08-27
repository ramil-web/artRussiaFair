<?php

namespace Admin\Http\Sort;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class StandRepresentativeSort implements Sort
{

    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $query->join('stand_representatives', 'my_teams.user_application_id', '=', 'stand_representatives.user_application_id')
        ->orderBy('stand_representatives.full_name', $descending ? 'desc' : 'asc');
    }
}
