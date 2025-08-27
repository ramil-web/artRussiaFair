<?php

namespace Lk\Http\Resources\UserProfile;

use Lk\Http\Resources\BaseResource;

class UserProfileResource extends BaseResource
{
    protected array $relationships = [
        'users'
    ];
    protected string $type = 'user-profile';
    protected string $namespace='lk.';
}
