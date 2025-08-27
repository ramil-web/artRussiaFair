<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use phpDocumentor\Reflection\Types\Integer;

/**
* @property integer $id
* @property integer $user_application_id
* @property array $url
 */
class Visualization extends Model
{
    use HasFactory;
    use SoftDeletes;

    const MODEL_NAME = 'Визуализация к заявке',
        MODEL_TYPE = 'application-images';


    protected $fillable = [
        'user_application_id',
        'url',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $casts = [
        'url' => Json::class
    ];

    public function userApplication(): BelongsTo
    {
        return $this->belongsTo(UserApplication::class);
    }
}
