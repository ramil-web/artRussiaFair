<?php

namespace Admin\Http\Resources\Product;

use App\Http\Resources\BaseCollection;

/** @see \App\Models\Product */
class ProductCollection extends BaseCollection
{
    protected string $type = 'products';
    protected string $namespace = 'Admin.';

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection
        ];
    }
}
