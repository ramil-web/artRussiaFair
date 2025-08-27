<?php

namespace Admin\Classic\Http\Resources\ClassicAssessmentApplication;

use Admin\Http\Resources\BaseCollection;

class ClassicAssessmentApplicationCollection extends BaseCollection
{
    protected string $type = 'classic-commission-assessments';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
