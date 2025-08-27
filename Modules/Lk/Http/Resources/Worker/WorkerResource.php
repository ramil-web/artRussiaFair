<?php

namespace Lk\Http\Resources\Worker;

use Lk\Http\Resources\BaseResource;

class WorkerResource extends BaseResource
{
    protected array $relationships = [
        'user_applications'
    ];
    protected string $type = 'worker';
    protected string $namespace='lk.';
}
