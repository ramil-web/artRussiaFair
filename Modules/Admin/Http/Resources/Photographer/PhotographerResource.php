<?php

namespace Admin\Http\Resources\Photographer;

use Admin\Http\Resources\BaseResource;

class PhotographerResource extends BaseResource
{
    protected array $relationships = [
        'events',
    ];
    protected string $type = 'photographer';
    protected string $namespace='Admin.';
}
