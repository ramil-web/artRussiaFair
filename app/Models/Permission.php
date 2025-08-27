<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property-read int    $id
 * @property string      $name
 * @property string      $description
 * @property string      $guard_name
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
class Permission extends SpatiePermission
{
    use HasFactory;

    const MODEL_NAME = 'Разрешения',
        MODEL_TYPE = 'permissions';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'pivot',
        'guard_name',
        'created_at',
        'updated_at'
    ];
}
