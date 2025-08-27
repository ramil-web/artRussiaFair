<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{


    const MODEL_NAME = 'Роли',
        MODEL_TYPE = 'roles';

    protected $guarded = [];
    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot',
    ];


}
