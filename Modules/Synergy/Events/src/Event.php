<?php

namespace Synergy\Events;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DbCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Event extends Model implements Sortable
{
    use SortableTrait;
    use HasFactory;


    public $guarded = [];

    public static function getLocale()
    {
        return app()->getLocale();
    }

    public function scopeWithType(Builder $query, string $type = null): Builder
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('type', $type)->ordered();
    }

    /**
     */
    public static function findOrCreate(
        array $data
    ): Builder|Model
    {
        return static::query()->create($data);
    }


    public static function updateEvent(
        Model $model, array $data
    ): Builder|array|DbCollection|Model
    {
        $model->update($data);
        return self::query()->findOrFail($model->id);
    }

    public static function getTypes(): Collection
    {
        return static::groupBy('type')->pluck('type');
    }
}
