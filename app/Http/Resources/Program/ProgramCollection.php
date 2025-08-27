<?php

namespace App\Http\Resources\Program;

use App\Http\Resources\BaseCollection;

class ProgramCollection extends BaseCollection
{
    protected string $type = 'program';
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
