<?php

namespace Admin\Http\Resources\Sculptor;

use Admin\Http\Resources\BaseCollection;

class SculptorCollection extends BaseCollection
{
    protected string $type = 'sculptor';
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
