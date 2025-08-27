<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class AppComment extends Model
{
    use HasTranslations;

    const MODEL_NAME = 'Комментарии к заявкам',
        MODEL_TYPE = 'app_comments';


    public array $translatable = ['message'];

    public $table = 'app_comments';

    protected $with = ['user.managerProfile', 'user.userProfile'];

    protected $fillable = [
        'user_id',
        'user_application_id',
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

    public function user_app(): BelongsTo
    {
        return $this->belongsTo(UserApplication::class);
    }
}
