<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class UserFilter implements Filter
{

    public function __invoke(Builder $query, $value, string $property): void
    {
        $query->whereHas('userApplication', function (Builder $query) use ($value) {
            $query->where('user_id', $value);
        });
    }
}
