<?php

namespace Admin\Http\Resources\ManagerProfile;

use Admin\Http\Resources\BaseResource;

class ManagerProfileResource extends BaseResource
{
    protected array $relationships = [
        'user'
    ];
    protected string $type = 'manager-profile';
    protected string $namespace = 'Admin.';
}
