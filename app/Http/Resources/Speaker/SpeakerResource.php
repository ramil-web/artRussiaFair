<?php

namespace App\Http\Resources\Speaker;

use App\Http\Resources\BaseResource;

class SpeakerResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'speaker';
    protected string $namespace='';
}
