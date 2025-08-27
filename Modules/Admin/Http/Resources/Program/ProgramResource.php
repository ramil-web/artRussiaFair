<?php

namespace Admin\Http\Resources\Program;

use Admin\Http\Resources\BaseResource;

class ProgramResource extends BaseResource
{
    protected array $relationships = [
        'event',
        'speaker'
    ];
    protected string $type = 'program';
    protected string $namespace = 'Admin.';
}
