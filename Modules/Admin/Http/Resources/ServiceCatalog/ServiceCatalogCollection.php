<?php

namespace Admin\Http\Resources\ServiceCatalog;

use App\Http\Resources\BaseCollection;
use App\Models\ServiceCatalog;

class ServiceCatalogCollection extends BaseCollection
{

    protected string $type = 'service-catalogs';
    protected string $namespace = 'Admin.';
    public function toArray($request)
    {
        return [
            'data' => $this->collection,

        ];
    }
}
