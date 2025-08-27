<?php

namespace Admin\Services;


use Admin\Repositories\Role\PermissionRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;

class PermissionService
{
    private PermissionRepository $permissionRepository;

    public function __construct(PermissionRepository $permissionRepository

    ) {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @throws Exception
     */
    public function list($request)
    {
        $withRelation = ['roles'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::trashed(),

        ];
        $allowedFields = ['id', 'name'];
        $allowedIncludes = [

        ];
        $allowedSorts = ['id'];

        $request->has('per_page') ? $perPage = $request->per_page : $perPage = null;

        return $this->permissionRepository->getAllByFilters(
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $perPage
        );
    }


    public function store(array $data): Model
    {

        return $this->permissionRepository->create($data);
    }

    public function update(int $id, UpdateRequest $request): Model
    {
        $data = $request->validated();

        $this->permissionRepository->update(Role::find($id), $data);


        return $this->permissionRepository->findById($id);
    }

    public function destroy(int $id): int
    {
        $data = [];

        $role = $this->permissionRepository->get($id);

        data_set($data, 'model', $role);

        return $this->rolePipeline->destroy($data);
    }


    public function view(int $id, Request $request)
    {
        $withRelation = ['users', 'roles'];
        $allowedFields = ['id', 'name'];
        $allowedIncludes = [
            'users',
            'roles'

        ];
        $request->has('with_trashed') ? $withTrashed = true : $withTrashed = false;

        return $this->permissionRepository->findById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $withTrashed
        );
    }
}
