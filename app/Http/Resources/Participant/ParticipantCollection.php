<?php

namespace App\Http\Resources\Participant;

use App\Http\Resources\BaseCollection;

class ParticipantCollection extends BaseCollection
{
    protected string $type = 'participant';
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
