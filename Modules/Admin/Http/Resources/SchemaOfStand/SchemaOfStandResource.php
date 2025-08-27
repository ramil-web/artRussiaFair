<?php

namespace Admin\Http\Resources\SchemaOfStand;

use Admin\Http\Resources\BaseResource;

class SchemaOfStandResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'schema';
    protected string $namespace='Admin.';
}
