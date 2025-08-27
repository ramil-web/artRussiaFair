<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Synergy\Events\HasEvents;

class ClassicUserApplication extends Model
{
    use HasTranslations;
    use HasEvents;
    use HasFactory;

    const MODEL_NAME = 'Заявка на участие ля классики',
        MODEL_TYPE = 'classic-user-application';

    public array $translatable = [
        'name_gallery',
        'representative_name',
        'representative_surname',
        'representative_city',
        'about_style',
        'about_description'
    ];

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
        'classic_event_id',
        'visitor'
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

    public function classicImages(): HasMany
    {
        return $this->hasMany(ClassicUserApplicationImage::class);
    }

    public function classicEvents(): BelongsTo
    {
        return $this->belongsTo(ClassicEvent::class, 'classic_event_id');
    }

    public function classicComments(): HasMany
    {
        return $this->hasMany(ClassicAppComment::class);
    }

    public function classicAssessments(): HasMany
    {
        return $this->hasMany(ClassicCommissionAssessment::class);
    }
}
