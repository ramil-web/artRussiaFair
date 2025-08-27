<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $event_id
 * @property integer $sort_id
 * @property string $name
 * @property string $description
 * @property string $full_description
 * @property string $type
 * @property string $image
 * @property string $position
 * @property integer $locate
 */
class Speaker extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'sort_id',
        'name',
        'description',
        'full_description',
        'image',
        'type',
        'position',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = ['type'];

    public array $translatable = ['name', 'description', 'full_description'];

    protected $casts = [
        'name'             => Json::class,
        'description'      => Json::class,
        'full_description' => Json::class,
        'position'         => Json::class,
    ];

    /**
     * @return MorphMany
     */
    public function eventgable(): MorphMany
    {
        return $this->morphMany(EventGable::class, 'eventgable');
    }

    /**
     * @param string $category
     * @return MorphMany|\Illuminate\Database\Eloquent\Builder
     */
    public function eventgableByCategory(string $category): MorphMany|\Illuminate\Database\Eloquent\Builder
    {
        return $this->eventgable()
            ->whereHas('event', function ($q) use ($category) {
                $q->where('category', $category);
            });
    }
}
