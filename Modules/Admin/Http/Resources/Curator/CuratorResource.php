<?php

namespace Admin\Http\Resources\Curator;

use Admin\Http\Resources\BaseResource;

class CuratorResource extends BaseResource
{
    protected array $relationships = [
        'eventgable',
    ];
    protected string $type = 'curator';
    protected string $namespace='Admin.';
}
