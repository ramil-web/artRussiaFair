<?php

namespace Admin\Http\Resources\AssessmentApplication;

use Admin\Http\Resources\BaseResource;

class AssessmentApplicationResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'commission-assessments';
    protected string $namespace='Admin.';
}
