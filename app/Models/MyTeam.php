<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * @property int $id
 * @property int $square
 * @property int $user_application_id
 * @property int $check_in
 * @property int $exit
 */
class MyTeam extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $type = 'my-team';

    protected $fillable = [
        'square',
        'check_in',
        'exit',
        'user_application_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m',
        'updated_at' => 'datetime:Y-m-d H:m',
    ];


    /**
     * @return HasMany
     */
    public function builders(): HasMany
    {
        return $this->hasMany(Builder::class, 'user_application_id', 'user_application_id')
            ->select(['id', 'user_application_id', 'full_name', 'passport']);
    }

    /**
     * @return HasMany
     */
    public function standRepresentatives(): HasMany
    {
        return $this->hasMany(StandRepresentative::class, 'user_application_id', 'user_application_id')
            ->select(['id', 'user_application_id', 'full_name', 'passport']);
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


    /**
     * @return BelongsToThrough
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

    /**
     * @return BelongsTo
     */
    public function check_in(): BelongsTo
    {
        return $this->belongsTo(TimeSlotStart::class, 'check_in', 'id', 'time_slot_starts')
            ->select('id', 'date', 'interval_times');
    }

    /**
     * @return BelongsTo
     */
    public function exit(): BelongsTo
    {
        return $this->belongsTo(TimeSlotStart::class, 'exit', 'id', 'time_slot_starts')
            ->select('id', 'date', 'interval_times');
    }
}
