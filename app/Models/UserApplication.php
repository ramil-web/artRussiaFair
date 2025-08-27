<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Synergy\Events\HasEvents;

/**
 * @property string $status
 * @property integer $event_id
 * @property integer $visitor
 * @property integer $education
 */
class UserApplication extends Model
{
    use HasTranslations;
    use HasEvents;

    const MODEL_NAME = 'Заявка на участие',
        MODEL_TYPE = 'user-applications';

    public $translatable = [
        'name_gallery',
        'representative_name',
        'representative_surname',
        'representative_city',
        'about_style',
        'about_description'
    ];
    const ENTITY_RELATIVE_USERS = 'user';

    public $table = 'user_applications';
    protected $fillable = [
        'number',
        'user_id',
        'type',
        'name_gallery',
        'representative_name',
        'representative_surname',
        'representative_email',
        'representative_phone',
        'representative_city',
        'about_style',
        'about_description',
        'other_fair',
        'social_links',
        'status',
        'active',
        'event_id',
        'visitor',
        'education'
    ];

    protected $casts = [
        'other_fair'   => Json::class,
        'social_links' => Json::class,
        'visitor'      => Json::class,
        'created_at'   => 'datetime:Y-m-d H:m',
        'updated_at'   => 'datetime:Y-m-d H:m',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->user()) {
                $model->user_id = auth()->user()->id;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(UserApplicationImages::class);
    }

    public function comment(): HasMany
    {
        return $this->hasMany(AppComment::class, 'user_application_id', 'id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(TimeSlotStart::class);
    }

    public function assessment(): HasMany
    {
        if (\Cache::has('user_application_list') && \Cache::get('user_application_list') == 'list') {
            return $this->hasMany(CommissionAssessment::class, 'user_application_id', 'id')
                ->select(['id', 'user_id', 'user_application_id']);
        } else {
            return $this->hasMany(CommissionAssessment::class, 'user_application_id', 'id');
        }
    }


    public function visualization(): HasMany
    {
        return $this->hasMany(Visualization::class, 'user_application_id', 'id');
    }

    public function visualizationAssessment(): HasMany
    {

        if (\Cache::has('user_application_list') && \Cache::get('user_application_list') == 'list') {
            return $this->hasMany(VisualizationAssessment::class, 'user_application_id', 'id')
                ->select(['id', 'user_id', 'user_application_id']);
        } else {
            return $this->hasMany(VisualizationAssessment::class, 'user_application_id', 'id');
        }
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class)->select(['id', 'category']);
    }
}

