<?php

namespace Lk\Http\Resources\Hardware;

use Lk\Http\Resources\BaseResource;

class HardwareResource extends BaseResource
{
    protected array $relationships = [
        'orders',
        'products'
    ];
    protected string $type = 'hardware';
    protected string $namespace='lk.';
}
