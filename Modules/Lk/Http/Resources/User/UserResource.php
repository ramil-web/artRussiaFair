<?php

namespace Lk\Http\Resources\User;

use Lk\Http\Resources\BaseResource;

class UserResource extends BaseResource
{
    protected array $relationships = [
        'roles',
        'userProfile'
    ];
    protected string $type = 'users';
    protected string $namespace='lk.';
}
