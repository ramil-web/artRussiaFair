<?php

namespace Admin\Http\Resources\Permission;

use Admin\Http\Resources\BaseCollection;
use App\Models\Permission;

class PermissionCollection extends BaseCollection
{
    protected string $type = Permission::MODEL_TYPE;
    protected string $namespace = 'Admin.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,

        ];
    }
}
