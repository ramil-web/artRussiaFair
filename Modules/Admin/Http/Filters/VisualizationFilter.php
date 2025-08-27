<?php

namespace Admin\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class VisualizationFilter implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return match ($value) {
            'with' => $query,
            'without' => $query->whereDoesntHave($property),
            'only' => $query->withWhereHas($property),
        };
    }
}
