<?php

namespace App\Models;


use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property integer $id
 * @property integer $sort_id
 * @property integer $partner_category_id
 * @property string $partner_category
 * @property string $image
 * @property string $name
 * @property string $link
 * @property integer $event_id
 * @property boolean $important
 */
class Partner extends Model
{
    use HasFactory;
    use SoftDeletes;

    const MODEL_TYPE = 'partner',
        MODEL_NAME = 'Партнер';


    protected $fillable = [
        'id',
        'sort_id',
        'partner_category_id',
        'name',
        'image',
        'link',
        'created_at',
        'updated_at',
        'deleted_at',
        'important'
    ];

    protected $casts = [
        'name' => Json::class
    ];

    /**
     * @return BelongsTo
     */
    public function partnerCategory(): BelongsTo
    {
        return $this->belongsTo(PartnerCategory::class)->select(['id', 'name', 'created_at', 'updated_at']);
    }

    /**
     * @return MorphMany
     */
    public function eventgable(): MorphMany
    {
        return $this->morphMany(EventGable::class, 'eventgable');
    }

    public function events()
    {
        return $this->morphedByMany(Event::class, 'eventgable', 'eventgables', 'eventgable_id', 'event_id');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_partners', 'partner_id', 'program_id');
    }
}
