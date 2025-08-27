<?php

namespace Admin\Http\Resources\ProjectTeam;

use Admin\Http\Resources\BaseResource;

class ProjectTeamResource extends BaseResource
{
    protected array $relationships = [
        'eventgable',
    ];
    protected string $type = 'project-team';
    protected string $namespace='Admin.';
}
