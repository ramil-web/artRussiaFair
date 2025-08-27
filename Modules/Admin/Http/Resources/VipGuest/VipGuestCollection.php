<?php

namespace Admin\Http\Resources\VipGuest;

use Admin\Http\Resources\BaseCollection;

class VipGuestCollection extends BaseCollection
{
    protected string $type = 'vip-guest';
    protected string $namespace = 'Admin.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
