<?php

namespace Lk\Http\Resources\Order;

use Lk\Http\Resources\BaseResource;

class OrderResource extends BaseResource
{
    protected array $relationships = [
        'user_applications',
        'time_slot_start',
        'time_slot_end',
        'users'
    ];
    protected string $type = 'order';
    protected string $namespace='lk.';

}
