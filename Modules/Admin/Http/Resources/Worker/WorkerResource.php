<?php

namespace Admin\Http\Resources\Worker;

use Admin\Http\Resources\BaseResource;

class WorkerResource extends BaseResource
{
    protected array $relationships = [
        'user_applications'
    ];
    protected string $type = 'worker';
    protected string $namespace = 'Admin.';
}
