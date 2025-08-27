<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_application_id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string $photo
 * @property string $social_network
 * @property string $url
 */
class InformationForPlacement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'user_application_id',
        'description',
        'url',
        'photo',
        'social_network',
    ];

    protected $casts = [
        'name' => Json::class,
        'description' => Json::class,
        'url' => Json::class,
        'social_network' => Json::class,
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m'
    ];

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
     * @return BelongsTo
     */
    public function userApplications(): BelongsTo
    {
        return $this->belongsTo(UserApplication::class, 'user_application_id', 'id');
    }
}
