<?php

namespace App\Http\Resources\Partner;

use App\Http\Resources\BaseCollection;

class PartnerCollection extends BaseCollection
{
    protected string $type = 'partner';
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
