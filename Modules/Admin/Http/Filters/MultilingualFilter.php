<?php

namespace Admin\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class MultilingualFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $locale = request('locale', 'ru');
        $field = $property;

        return $query->where(function ($query) use ($field, $value, $locale) {
            $query->orWhere("$field->$locale", 'LIKE', "%{$value}%");
        });
    }
}
