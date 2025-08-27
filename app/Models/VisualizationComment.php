<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

/**
 * @property int $id
 * @property int $user_id
 * @property int $visualization_id
 * @property int $user_application_id
 * @property string $message
 */
class VisualizationComment extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'user_application_id',
        'visualization_id',
        'message',
    ];

    public array $translatable = ['message'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m',
        'deleted_at' => 'datetime:Y-m-d H:m',
        'message'    => Json::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userApplication(): BelongsTo
    {
        return $this->belongsTo(UserApplication::class);
    }
}
