<?php

namespace Admin\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class MultiLangFilter implements Filter
{
    private string $column;

    public function __construct(string $column)
    {
        $this->column = $column;
    }

    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->where($this->column.'->'. app()->getLocale(), 'LIKE', "%{$value}%");
    }
}
