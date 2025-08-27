<?php

namespace Admin\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class ModeratorNameFilter extends FiltersUserRole
{
    /**
     * @param Builder $query
     * @param mixed $value
     * @param string $property
     * @return void
     */
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $query->where('moderator_name->'. app()->getLocale(), 'LIKE', "%{$value}%");
    }
}
