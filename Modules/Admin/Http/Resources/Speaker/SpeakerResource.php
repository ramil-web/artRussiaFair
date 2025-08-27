<?php

namespace Admin\Http\Resources\Speaker;

use Admin\Http\Resources\BaseResource;

class SpeakerResource extends BaseResource
{
    protected array $relationships = [
        'events',
    ];
    protected string $type = 'speaker';
    protected string $namespace='Admin.';
}
