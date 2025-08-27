<?php

namespace Lk\Http\Resources\AdditionalService;

use Lk\Http\Resources\BaseResource;

class AdditionalServiceResource extends BaseResource
{

    protected array $relationships = [
        'orders','service_catalogs'
    ];
    protected string $type = 'additional-service';
    protected string $namespace='lk.';
}
