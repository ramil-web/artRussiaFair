<?php

namespace Admin\Http\Resources\Speaker;

use Admin\Http\Resources\BaseCollection;

class SpeakerCollection extends BaseCollection
{

    protected string $type = 'speaker';
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
