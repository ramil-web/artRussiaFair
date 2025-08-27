<?php

namespace Admin\Http\Resources\VisualisationAssessment;

use Admin\Http\Resources\BaseCollection;

class VisualizationAssessmentCollection extends BaseCollection
{
    protected string $type = 'visualization-assessment';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
