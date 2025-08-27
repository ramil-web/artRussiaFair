<?php

namespace Admin\Http\Resources\Photographer;

use Admin\Http\Resources\BaseCollection;

class PhotographerCollection extends BaseCollection
{
    protected string $type = 'photographer';
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
