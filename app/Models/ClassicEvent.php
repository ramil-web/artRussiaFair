<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


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
 * @property string $event_type
 * @property string $start_accepting_applications
 * @property string $end_accepting_applications
 */
class ClassicEvent extends Model
{
    use HasFactory;
    use SoftDeletes;


    const  MODEL_TYPE = 'classic_events';

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
        'start_accepting_applications',
        'end_accepting_applications',
        'event_type'
    ];

    protected $casts = [
        'name'         => Json::class,
        'description'  => Json::class,
        'place'        => Json::class,
        'social_links' => Json::class,
    ];

    public static function findOrCreate(
        array $data
    ): Builder|Model
    {
        return static::query()->create($data);
    }

    public function classicUserApplications(): HasMany
    {
        return $this->hasMany(ClassicUserApplication::class);
    }
}
