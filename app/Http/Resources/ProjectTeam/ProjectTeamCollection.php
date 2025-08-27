<?php

namespace App\Http\Resources\ProjectTeam;

use App\Http\Resources\BaseCollection;

class ProjectTeamCollection extends BaseCollection
{
    protected string $type = 'project-team';
    protected string $namespace = '';

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {

        return [
            'data' => $this->collection,
        ];
    }
}
