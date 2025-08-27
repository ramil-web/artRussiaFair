<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassicUserApplicationImage extends Model
{
    /**
     * @property-read int $classic_user_application_id
     * @property string $url
     * @property string $description
     */
    use HasFactory;

    const MODEL_NAME = 'Изображения к заявке',
        MODEL_TYPE = 'classic_application-images';


    public $table = 'classic_user_application_images';
    protected $fillable = [
        'classic_user_application_id',
        'url',
        'title',
        'description'
    ];

    protected $with = 'application';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(ClassicUserApplication::class);
    }
}
