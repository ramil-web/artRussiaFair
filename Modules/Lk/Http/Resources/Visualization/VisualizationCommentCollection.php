<?php

namespace Lk\Http\Resources\Visualization;

use Lk\Http\Resources\BaseCollection;

class VisualizationCommentCollection extends BaseCollection
{
    protected string $type = 'visualization-comment';
    protected string $namespace = 'lk.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
