<?php

namespace Admin\Http\Resources\UserApplications;

use Admin\Http\Resources\BaseResource;

class UserApplicationResource extends BaseResource
{
    protected array $relationships = [
        'images',
        'event','comment'
    ];
    protected string $type = 'user-applications';
    protected string $namespace = 'Admin.';
}
