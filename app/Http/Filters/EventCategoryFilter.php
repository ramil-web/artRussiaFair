<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class EventCategoryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas('eventgable', function ($q) use ($value) {
            $q->whereHas('event', function ($q2) use ($value) {
                $q2->where('category', $value);
            });
        });
    }
}
