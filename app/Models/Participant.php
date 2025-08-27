<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $event_id
 * @property integer $sort_id
 * @property integer $stand_id
 * @property object $name
 * @property string $description
 * @property string $type
 * @property string $image
 * @property string $slug
 * @property string $images
 */

class Participant extends Model
{
    use HasFactory;
    use SoftDeletes;
    const MODEL_TYPE = 'participant';

    protected $fillable = [
        'id',
        'slug',
        'sort_id',
        'stand_id',
        'name',
        'description',
        'image',
        'images',
        'type',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public array $translatable = ['description'];

    /**
     * @return MorphMany
     */
    public function eventgable(): MorphMany
    {
        return $this->morphMany(EventGable::class, 'eventgable');
    }
}
