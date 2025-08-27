<?php

namespace Lk\Http\Resources\MyTeam\StandRepresentative;

use Lk\Http\Resources\BaseResource;

class StandRepresentativeResource extends BaseResource
{
    protected array $relationships = [
        'user_application'
    ];
    protected string $type = 'my-team.stand-representative';
    protected string $namespace = 'lk.';
}
