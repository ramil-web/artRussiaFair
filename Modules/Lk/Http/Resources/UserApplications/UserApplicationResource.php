<?php

namespace Lk\Http\Resources\UserApplications;

use Lk\Http\Resources\BaseResource;

class UserApplicationResource extends BaseResource
{
    protected array $relationships = [
        'images'
    ];
    protected string $type = 'user-applications';
    protected string $namespace='lk.';
}
