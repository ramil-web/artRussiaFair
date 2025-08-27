<?php

namespace Admin\Http\Resources\Curator;

use Admin\Http\Resources\BaseCollection;

class CuratorCollection extends BaseCollection
{

    protected string $type = 'curator';
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
