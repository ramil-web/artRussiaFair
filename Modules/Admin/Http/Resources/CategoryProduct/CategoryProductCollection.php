<?php

namespace Admin\Http\Resources\CategoryProduct;

use App\Http\Resources\BaseCollection;
use App\Models\CategoryProduct;

/** @see \App\Models\CategoryProduct */
class CategoryProductCollection extends BaseCollection
{
    protected string $type = 'category-products';
    protected string $namespace = 'Admin.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }
}
