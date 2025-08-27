<?php

namespace Lk\Http\Resources\Order;

use Lk\Http\Resources\BaseCollection;

class OrderCollection extends BaseCollection
{
    protected string $type = 'order';
    protected string $namespace = 'lk.';

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
