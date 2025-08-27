<?php

namespace Admin\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class CategoryFilter implements Filter
{

    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas('event', function ($query) use ($value) {
            $query->where('category', $value);
        });
    }
}
