<?php

namespace App\Http\Resources\Participant;

use App\Http\Resources\BaseResource;

class ParticipantResource extends BaseResource
{

    protected array $relationships = [];
    protected string $type = 'participant';
    protected string $namespace='';
}
