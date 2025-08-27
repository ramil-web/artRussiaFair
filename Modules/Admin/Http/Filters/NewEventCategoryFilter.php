<?php

namespace Admin\Http\Filters;


use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class NewEventCategoryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas('event', function (Builder $q) use ($value) {
            if (is_array($value)) {
                $q->whereIn('category', $value);
            } else {
                $q->where('category', $value);
            }
        });
    }
}
