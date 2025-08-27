<?php

namespace Admin\Http\Resources\User;

use Admin\Http\Resources\BaseCollection;

class UserCollection extends BaseCollection
{
    protected string $type = 'users';
    protected string $namespace = 'Admin.';

    public function toArray($request)
    {
//        dd($this->collection);

        return [
            'data' => $this->collection,

        ];
    }
}
