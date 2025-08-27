<?php

namespace Admin\Repositories\Role;

use Admin\Repositories\BaseRepository;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RoleRepository  extends BaseRepository

{
    protected Model $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function attachPermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->attach($permissionIds);
    }

    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->syncPermissions($permissionIds);
    }

    public function detachPermissions(Role $role): void
    {
        $role->permissions()->detach();
    }

    public function loadRelations(Role $role, array $relations): void
    {
        $role->load($relations);
    }
}
