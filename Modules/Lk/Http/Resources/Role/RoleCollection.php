<?php

namespace Lk\Http\Resources\Role;

use App\Models\Role;
use Lk\Http\Resources\BaseCollection;

class RoleCollection extends BaseCollection
{
    protected string $type = Role::MODEL_TYPE;
}
