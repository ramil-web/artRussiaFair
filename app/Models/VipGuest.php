<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Translatable\HasTranslations;
use Znck\Eloquent\Traits\BelongsToThrough;

/**
 * @property integer $id
 * @property integer $user_application_id
 * @property string $full_name
 * @property string $organisations
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 */
class VipGuest extends Model
{
    use HasFactory;
    use HasTranslations;
    use BelongsToThrough;

    const MODEL_NAME = 'Вип-гости',
        MODEL_TYPE = 'vip-guests';

    public $table = 'vip_guests';

    protected $fillable = [
        'full_name',
        'user_application_id',
        'organization',
        'email'
    ];

    public array $translatable = [
        'full_name',
        'organization'
    ];

    protected $casts = [
        'full_name'    => Json::class,
        'organization' => Json::class,
        'created_at'   => 'datetime:Y-m-d H:m',
        'updated_at'   => 'datetime:Y-m-d H:m',
    ];
    protected $hidden = ['users'];

    /**
     * @return HasManyThrough
     */
    public function userProfile(): HasManyThrough
    {
        return $this->hasOneThrough(
            UserProfile::class,
            UserApplication::class,
            'id',
            'user_id',
            'user_application_id',
            'user_id'
        )->select([
            'user_profiles.id',
            'user_profiles.name',
            'user_profiles.surname',
        ]);
    }

    public function userApplication(): BelongsTo
    {
        return $this->belongsTo(UserApplication::class, 'user_application_id')->select([
            'id',
            'user_id',
            'representative_name',
            'representative_surname',
        ]);
    }
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
