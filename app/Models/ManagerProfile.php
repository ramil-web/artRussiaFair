<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ManagerProfile extends Model
{

    use SoftDeletes;
    use HasTranslations;

    const MODEL_NAME = 'Профиль менеджера',
        MODEL_TYPE = 'manager_profile';

    const ENTITY_RELATIVE_USERS = 'user';

    public $table = 'manager_profiles';

    public $translatable = ['name', 'surname', 'city'];
    protected $fillable = ['avatar', 'name', 'surname', 'phone', 'city', 'user_id'];
//    protected $with = 'user';
    protected $hidden = [
//        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

//    protected static function boot()
//    {
//        parent::boot();
//
//        static::saving(function ($model) {
//            if(auth()->user()){
//                $model->user_id = auth()->user()->id;
//            }
//        });
//    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
