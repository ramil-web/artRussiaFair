<?php

namespace Admin\Http\Resources\Permission;

use Admin\Http\Resources\BaseResource;
use App\Models\Permission;

class PermissionResource extends BaseResource
{
    protected string $type = 'permissions';
    protected string $namespace = 'Admin.';

    protected array $relationships = [
        'roles',
    ];


}
