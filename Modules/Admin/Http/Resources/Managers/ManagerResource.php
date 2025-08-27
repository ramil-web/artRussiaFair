<?php

namespace Admin\Http\Resources\Managers;

use Admin\Http\Resources\BaseResource;

class ManagerResource extends BaseResource
{
    protected array $relationships = [
        'roles',
        'managerProfile'
    ];
    protected string $type = 'manager';
    protected string $namespace = 'Admin.';
}
