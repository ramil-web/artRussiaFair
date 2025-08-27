<?php

namespace Admin\Http\Resources\CategoryProduct;

use App\Http\Resources\BaseResource;

class CategoryProductResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'category-products';
    protected string $namespace = 'Admin.';
}
