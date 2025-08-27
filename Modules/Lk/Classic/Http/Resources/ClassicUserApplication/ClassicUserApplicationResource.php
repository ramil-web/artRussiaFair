<?php

namespace Lk\Classic\Http\Resources\ClassicUserApplication;

use Lk\Http\Resources\BaseResource;

class ClassicUserApplicationResource extends BaseResource
{
    protected array $relationships = [
        'images'
    ];
    protected string $type = 'classic-user-applications';
    protected string $namespace='lk.';
}
