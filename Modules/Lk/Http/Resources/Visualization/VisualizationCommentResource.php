<?php

namespace Lk\Http\Resources\Visualization;

use Lk\Http\Resources\BaseResource;

class VisualizationCommentResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'visualization-comment';
    protected string $namespace='lk.';
}
