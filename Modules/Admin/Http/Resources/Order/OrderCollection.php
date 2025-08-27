<?php

namespace Admin\Http\Resources\Order;

use Admin\Http\Resources\BaseCollection;

class OrderCollection extends BaseCollection
{

    protected string $type = 'order';
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
