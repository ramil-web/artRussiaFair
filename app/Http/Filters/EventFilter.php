<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class EventFilter implements Filter
{

    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('eventgable', function (Builder $query) use ($value) {
            $query->where('event_id', $value);
        });
    }
}
