<?php

namespace Admin\Http\Resources\Sculptor;

use App\Http\Resources\BaseResource;

class SculptorResource extends BaseResource
{
    protected array $relationships = [
        'events',
    ];
    protected string $type = 'sculptor';
    protected string $namespace='Admin.';
}
