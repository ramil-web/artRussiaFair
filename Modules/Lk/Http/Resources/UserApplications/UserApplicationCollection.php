<?php

namespace Lk\Http\Resources\UserApplications;

use Lk\Http\Resources\BaseCollection;


/** @see \App\Models\UserApplication */
class UserApplicationCollection extends BaseCollection
{
    protected string $type = 'user-applications';
    protected string $namespace = 'lk.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,

        ];
    }
}
