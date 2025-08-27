<?php

namespace Lk\Http\Resources\ManagerProfile;

use Lk\Http\Resources\BaseResource;

class ManagerProfileResource extends BaseResource
{
    protected array $relationships = [
        'user'
    ];
    protected string $type = 'manager-profile';
    protected string $namespace='lk.';
}
