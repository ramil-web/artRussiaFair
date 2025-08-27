<?php

namespace Admin\Http\Resources\ProjectTeam;

use Admin\Http\Resources\BaseCollection;

class ProjectTeamCollection extends BaseCollection
{

    protected string $type = 'project-team';
    protected string $namespace = 'Admin.';

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
