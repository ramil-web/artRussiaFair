<?php

namespace Admin\Http\Resources\Partner;

use Admin\Http\Resources\BaseCollection;

class PartnerCollection extends BaseCollection
{
    protected string $type = 'partner';
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
