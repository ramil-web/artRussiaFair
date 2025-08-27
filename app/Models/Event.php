<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Synergy\Events\Event as SinergyEvent;

/**
 * @property integer $id
 * @property integer $sort_id
 * @property string $slug
 * @property object $description
 * @property object $name
 * @property string $social_links
 * @property string $place
 * @property string $year
 * @property string $start_date
 * @property string $end_date
 * @property string $status
 * @property string $type
 * @property string $start_accepting_applications
 * @property string $end_accepting_applications
 * @property string $category
 */
class Event extends SinergyEvent
{

    use SoftDeletes;

    const MODEL_NAME = 'Ежегодное событие',
        MODEL_TYPE = 'events';


    public $table = 'events';
    protected $fillable = [
        'name',
        'sort_id',
        'slug',
        'description',
        'place',
        'social_links',
        'year',
        'start_date',
        'end_date',
        'status',
        'type',
        'start_accepting_applications',
        'end_accepting_applications',
        'event_type',
        'category'
    ];

    protected $casts = [
        'name'         => Json::class,
        'description'  => Json::class,
        'place'        => Json::class,
        'social_links' => Json::class,
    ];
    protected $hidden = [];

    public function eventgable(): MorphTo
    {
        return $this->morphTo();
    }

    public function time_slots(): HasMany
    {
        return $this->hasMany(TimeSlotStart::class);
    }

    public function partners(): MorphToMany
    {
        return $this->morphedByMany(Partner::class, 'eventgable')->select('id', 'partner_category_id');
    }
}
