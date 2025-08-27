<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Znck\Eloquent\Traits\BelongsToThrough;


class Person extends Model
{
    use HasFactory;
    use HasTranslations;
    use BelongsToThrough;

    const MODEL_NAME = 'Персоны',
        MODEL_TYPE = 'persons';

    public $table = 'persons';

    protected $fillable = [
        'full_name',
        'passport',
        'type',
        'user_application_id',
    ];

    public $translatable = [
        'full_name',
    ];

    protected $hidden = ['users', 'type'];

    protected $casts = [
        'full_name' => Json::class,
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m',
    ];

    /**
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     */
    public function users()
    {
        return $this->belongsToThrough(User::class, UserApplication::class);
    }

    /**
     * @return BelongsTo
     */
    public function user_applications(): BelongsTo
    {
        return $this->belongsTo(UserApplication::class, 'user_application_id', 'id');
    }

}
