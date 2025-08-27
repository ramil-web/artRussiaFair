<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property integer $event_id
 * @property integer $eventgable_id
 * @property string $eventgable_type
 */
class EventGable extends Model
{
    use SortableTrait;
    use HasFactory;

    const MODEL_NAME = 'Связущая таблица',
        MODEL_TYPE = 'eventgables';

    protected $table = 'eventgables';
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'eventgable_id',
        'eventgable_type'
    ];

    /**
     * @return MorphTo
     */
    public function eventgable(): MorphTo
    {
        return $this->morphTo();
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
