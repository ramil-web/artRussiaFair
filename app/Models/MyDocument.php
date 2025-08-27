<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_application_id
 * @property string $status
 * @property string $files
 */
class MyDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_application_id',
        'status',
        'files'
    ];

    protected $casts = [
        'files' => Json::class,
    ];

    /**
     * @return HasMany
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    /**
     * @return HasMany
     */
    public function requisites(): HasMany
    {
        return $this->hasMany(Requisite::class);
    }
}
