<?php

namespace Admin\Classic\Http\Resources\ClassicUserApplication;

use Admin\Http\Resources\BaseResource;

class ClassicUserApplicationResource extends BaseResource
{
    protected array $relationships = [
        'images',
        'classic_event'
    ];
    protected string $type = 'classic-user-applications';
    protected string $namespace = 'Admin.';
}
