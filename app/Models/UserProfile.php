<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;


class UserProfile extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasTranslations;

    const MODEL_NAME = 'Профиль пользователя',
        MODEL_TYPE = 'user-profile';

    public $translatable = ['name', 'surname', 'city'];
    protected $fillable = ['avatar', 'name', 'surname', 'phone', 'city', 'user_id'];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'name'    => Json::class,
        'surname' => Json::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (auth()->user()) {
                $model->user_id = auth()->user()->id;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
