<?php

namespace Admin\Http\Resources\Partner;

use Admin\Http\Resources\BaseResource;

class PartnerResource extends BaseResource
{
    protected array $relationships = [
        'events',
        'partnerCategory'
    ];
    protected string $type = 'partner';
    protected string $namespace='Admin.';

}
