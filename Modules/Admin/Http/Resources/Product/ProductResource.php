<?php

namespace Admin\Http\Resources\Product;

use App\Http\Resources\BaseResource;

class ProductResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'products';
    protected string $namespace = 'Admin.';
}
