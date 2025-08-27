<?php

namespace Admin\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class NameFilter implements Filter
{
    /**
     * @param Builder $query
     * @param mixed $value
     * @param string $property
     * @return void
     */
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
            $query->where('name->'. app()->getLocale(), 'LIKE', "%{$value}%");
    }
}
