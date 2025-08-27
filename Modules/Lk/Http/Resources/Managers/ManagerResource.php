<?php

namespace Lk\Http\Resources\Managers;

use Lk\Http\Resources\BaseResource;

class ManagerResource extends BaseResource
{
    protected array $relationships = [
        'roles',
        'managerProfile'
    ];
    protected string $type = 'manager';
    protected string $namespace='lk.';
}
