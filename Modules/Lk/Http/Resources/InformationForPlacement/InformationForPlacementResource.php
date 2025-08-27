<?php

namespace Lk\Http\Resources\InformationForPlacement;

use Lk\Http\Resources\BaseResource;

class InformationForPlacementResource extends BaseResource
{
    protected array $relationships = [
        'userApplications',
    ];
    protected string $type = 'information-placement';
    protected string $namespace='lk.';
}
