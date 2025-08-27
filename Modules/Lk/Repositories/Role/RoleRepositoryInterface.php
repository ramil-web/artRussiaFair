<?php

namespace Lk\Repositories\Role;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RoleRepositoryInterface
{
    public function store(array $data): Model;

    public function update(Role $role, array $data): Model;

    public function destroy(Role $role): bool;

    public function index(): Collection;

    public function get(int $id): Model;

    public function attachPermissions(Role $role, array $permissionIds): void;

    public function syncPermissions(Role $role, array $permissionIds): void;

    public function detachPermissions(Role $role): void;

    public function loadRelations(Role $role, array $relations): void;
}
