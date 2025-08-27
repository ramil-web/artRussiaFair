<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property integer $id
 * @property integer $event_id
 * @property string $name
 * @property string $moderator_name
 * @property string $moderator_description
 * @property string $start_time
 * @property string $end_time
 * @property string $date
 * @property string $program_format
 * @property string $description
 */
class Program extends Model
{
    use HasFactory;
    use SoftDeletes;


    const MODEL_TYPE = 'program',
        MODEL_NAME = 'Программа';

    protected $fillable = [
        'event_id',
        'start_time',
        'end_time',
        'date',
        'name',
        'moderator_name',
        'moderator_description',
        'program_format',
        'description'
    ];

    protected $casts = [
        'name' => Json::class,
        'description' => Json::class,
        'moderator_name' => Json::class,
        'moderator_description' => Json::class,
    ];

    /**
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsToMany
     */

    public function speaker(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'program_speaker');
    }
    public function partner(): BelongsToMany
    {
        return $this->belongsToMany(Partner::class, 'program_partners');
    }

    /**
     * @return BelongsTo
     */
    public function eventType(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id')->select(['id','event_type']);
    }
}
