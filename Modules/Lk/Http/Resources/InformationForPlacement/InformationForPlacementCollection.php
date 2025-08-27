<?php

namespace Lk\Http\Resources\InformationForPlacement;

use Lk\Http\Resources\BaseCollection;

class InformationForPlacementCollection extends BaseCollection
{
    protected string $type = 'information-for-placement';
    protected string $namespace = 'lk.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
