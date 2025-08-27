<?php

namespace Admin\Http\Resources\Role;

use Admin\Http\Resources\BaseResource;
use App\Models\Role;

class RoleResource extends BaseResource
{
    protected string $type = 'roles';
    protected string $namespace = 'Admin.';
    protected array $relationships = [
        'permissions',
    ];

}
