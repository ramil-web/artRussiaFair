<?php

namespace Admin\Http\Resources\VipGuest;

use Admin\Http\Resources\BaseResource;

class VipGuestResource extends BaseResource
{
    protected array $relationships = [
        'userApplications',
    ];
    protected string $type = 'vip-guest';
    protected string $namespace = 'Admin.';
}
