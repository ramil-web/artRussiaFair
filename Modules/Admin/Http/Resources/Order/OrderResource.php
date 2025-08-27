<?php

namespace Admin\Http\Resources\Order;

use Admin\Http\Resources\BaseResource;

class OrderResource extends BaseResource
{

    protected array $relationships = [
        'user_applications',
        'time_slot_start'
    ];
    protected string $type = 'order';
    protected string $namespace='Admin.';
}
