<?php

namespace Admin\Http\Resources\VisualizationComment;

use Admin\Http\Resources\BaseCollection;

class VisualizationCommentCollection extends BaseCollection
{
    protected string $type = 'visualization-comment';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
