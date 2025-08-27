<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class EventTypeFilter implements Filter
{

    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas('eventType', function (Builder $query) use ($value) {
            $query->where('event_type', 'like', "%{$value}%");
        });
    }
}
