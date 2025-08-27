<?php

namespace Lk\Http\Resources\VipGuest;

use Lk\Http\Resources\BaseCollection;


/** @see \App\Models\UserApplication */
class VipGuestCollection extends BaseCollection
{
    protected string $type = 'vip-guests';
    protected string $namespace = 'lk.';


    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
