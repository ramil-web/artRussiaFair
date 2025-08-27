<?php

namespace Lk\Http\Resources\MyTeam\Builder;

use Lk\Http\Resources\BaseResource;

class BuilderResource extends BaseResource
{
    protected array $relationships = [
        'user_application'
    ];
    protected string $type = 'my-team.builder';
    protected string $namespace = 'lk.';
}
