<?php

namespace Admin\Repositories\Role;

use Admin\Repositories\BaseRepository;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;

class PermissionRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    private array $allowedFields = [
        'id',
        'name',
        'description',
        'roles.id',
        'roles.name',
        'roles.guard_name',
        'roles.description',
    ];




    public function destroy(Model $model): bool
    {
        return $model->delete();
    }

    public function list(): Collection
    {
         return QueryBuilder::for(Permission::class)
            ->allowedFilters(['name'])
            ->allowedFields($this->allowedFields)
            ->allowedIncludes([
                'roles',
            ])
            ->allowedSorts([
                'id',
            ])->get();
    }

    public function get(int $id): Model
    {
        return QueryBuilder::for(Permission::class)
            ->allowedFields($this->allowedFields)
            ->allowedIncludes([
                'roles',
            ])
            ->findOrFail($id);
    }
}
