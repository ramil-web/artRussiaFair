<?php

namespace App\Http\Resources\Speaker;

use App\Http\Resources\BaseCollection;

class SpeakerCollection extends BaseCollection
{
    protected string $type = 'speaker';
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
