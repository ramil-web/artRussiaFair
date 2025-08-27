<?php

namespace Admin\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class SpecificationsFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        foreach ($value as $language => $values) {
            $query->orWhere(function ($query) use ($language, $values) {
                foreach ($values as $field => $fieldValue) {
                    $query->where("specifications->$language->$field", $fieldValue);
                }
            });
        }
    }
}
