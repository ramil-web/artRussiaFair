<?php

namespace Admin\Http\Resources\InformationForPlacement;

use Admin\Http\Resources\BaseCollection;

class InformationForPlacementCollection extends BaseCollection
{

    protected string $type = 'information-for-replacement';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
