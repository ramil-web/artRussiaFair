<?php

namespace Lk\Http\Resources\Visualization;

use Lk\Http\Resources\BaseResource;

class VisualizationApplicationResource extends BaseResource
{
    protected array $relationships = [
        'userApplication'
    ];
    protected string $type = 'app-visualization';
    protected string $namespace='lk.';
}
