<?php

namespace Lk\Http\Resources\MyTeam;

use Lk\Http\Resources\BaseResource;

class MyTeamResource extends BaseResource
{
    protected array $relationships = [
        'user_application'
    ];
    protected string $type = 'my-team';
    protected string $namespace = 'lk.';
}
