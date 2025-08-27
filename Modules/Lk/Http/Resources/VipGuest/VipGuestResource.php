<?php

namespace Lk\Http\Resources\VipGuest;

use Lk\Http\Resources\BaseResource;

class VipGuestResource extends BaseResource
{

    protected array $relationships = [
        'user_applications'
    ];
    protected string $type = 'vip-guests';
    protected string $namespace='lk.';
}
