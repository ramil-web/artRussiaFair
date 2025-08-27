<?php

namespace Admin\Http\Resources\Role;

use Admin\Http\Resources\BaseCollection;
use App\Models\Role;

class RoleCollection extends BaseCollection
{
    protected string $type = 'roles';
    protected string $namespace = 'Admin.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,

        ];
    }
}
