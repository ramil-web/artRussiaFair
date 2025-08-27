<?php

namespace Admin\Http\Resources\ServiceCatalog;

use App\Http\Resources\BaseResource;

class ServiceCatalogResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'service-catalogs';
    protected string $namespace = 'Admin.';
}
