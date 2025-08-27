<?php

namespace Lk\Http\Resources\Visualization;

use Lk\Http\Resources\BaseCollection;

/** @see \App\Models\AppComment */
class VisualizationApplicationCollection extends BaseCollection
{
    protected string $type = 'app-visualizition';
    protected string $namespace = 'lk.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
