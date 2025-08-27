<?php

namespace Admin\Http\Resources\User;

use Admin\Http\Resources\BaseResource;

class UserResource extends BaseResource
{
    protected array $relationships = [
        'roles',
        'userProfile'
    ];
    protected string $type = 'users';
    protected string $namespace = 'Admin.';
}
