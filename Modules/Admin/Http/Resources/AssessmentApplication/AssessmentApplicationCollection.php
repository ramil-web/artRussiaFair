<?php

namespace Admin\Http\Resources\AssessmentApplication;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Lk\Http\Resources\BaseCollection;

/** @see \App\Models\AppComment */
class AssessmentApplicationCollection extends BaseCollection
{
    protected string $type = 'commission-assessments';
    protected string $namespace = 'Admin.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
