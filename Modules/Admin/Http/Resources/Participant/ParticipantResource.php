<?php

namespace Admin\Http\Resources\Participant;

use Admin\Http\Resources\BaseResource;

class ParticipantResource extends BaseResource
{

    protected string $type;
    protected string $namespace='Admin.';
    public function __construct($resource, $type = 'artist')
    {
        parent::__construct($resource, $type);
        $this->type = $type;
    }

    protected array $relationships = [
        'events',
    ];
}
