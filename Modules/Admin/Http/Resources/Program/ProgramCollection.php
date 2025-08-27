<?php

namespace Admin\Http\Resources\Program;

use Admin\Http\Resources\BaseCollection;

class ProgramCollection extends BaseCollection
{
    protected string $type = 'program';
    protected string $namespace = 'Admin.';

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection
        ];
    }
}
