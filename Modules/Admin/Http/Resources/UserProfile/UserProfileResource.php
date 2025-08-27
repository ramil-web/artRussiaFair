<?php

namespace Admin\Http\Resources\UserProfile;

use Admin\Http\Resources\BaseResource;

class UserProfileResource extends BaseResource
{
    protected array $relationships = [
        'user'
    ];
    protected string $type = 'user-profile';
    protected string $namespace = 'Admin.';
}
