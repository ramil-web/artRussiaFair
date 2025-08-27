<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class ClassicAppComment extends Model
{
    use HasTranslations;

    const MODEL_NAME = 'Комментарии к заявкам',
        MODEL_TYPE = 'classic_app_comments';


    public array $translatable = ['message'];

    public $table = 'classic_app_comments';

    protected $with = ['user.managerProfile', 'user.userProfile'];

    protected $fillable = [
        'user_id',
        'classic_user_application_id',
        'message',
    ];
    protected $casts = [
        'message'    => Json::class,
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classicUserApp(): BelongsTo
    {
        return $this->belongsTo(ClassicUserApplication::class);
    }
}
