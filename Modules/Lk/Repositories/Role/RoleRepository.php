<?php

namespace Lk\Repositories\Role;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RoleRepository implements RoleRepositoryInterface
{
    private array $allowedFields = [
        'id',
        'name',
        'users.id',
        'users.username',
        'users.email',
        'permissions.id',
        'permissions.name',
    ];


    public function store(array $data): Model
    {
        return Role::query()->create($data);
    }

    public function update(Role $role, array $data): Model
    {
        $role->update($data);

        return $role;
    }

    public function destroy(Role $role): bool
    {
        return $role->delete();
    }

    public function index(): Collection
    {
        return QueryBuilder::for(Role::class)
            ->allowedFilters([
                AllowedFilter::exact('name'),
            ])
            ->allowedFields($this->allowedFields)
            ->allowedIncludes([
                'permissions',
                'users',
            ])
            ->defaultSort('id')
            ->allowedSorts([
                'id',
            ])->get();
    }

    public function get(int $id): Model
    {
        return QueryBuilder::for(Role::class)
            ->allowedFields($this->allowedFields)
            ->allowedIncludes([
                'permissions',
                'users',
            ])
            ->findOrFail($id);
    }

    public function attachPermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->attach($permissionIds);
    }

    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
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
