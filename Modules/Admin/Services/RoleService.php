<?php

namespace Admin\Services;

use Admin\Http\Requests\Role\StoreRequest;
use Admin\Repositories\Role\RoleRepository;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;

class RoleService
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository

    ) {
        $this->roleRepository = $roleRepository;
    }

    public function list($request)
    {
        $withRelation = ['users', 'permissions'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::trashed(),

        ];
        $allowedFields = ['id', 'name', 'desc'];
        $allowedIncludes = [

        ];
        $allowedSorts = ['id'];

        $request->has('per_page') ? $perPage = $request->per_page : $perPage = null;

        return $this->roleRepository->getAllByFilters(
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $perPage
        );
    }


    public function store(StoreRequest $request): Model
    {
        $data = $request->validated();

        return $this->roleRepository->create($data);
    }

    public function update(int $id, array $data): Model
    {
        $this->roleRepository->update(Role::find($id), Arr::only($data, ['desc']));

        $this->roleRepository->syncPermissions(Role::find($id), Arr::only($data, ['permissions']));

        $withRelation = ['permissions'];
        $allowedFields = ['id', 'name'];
        $allowedIncludes = [
//            'permissions'

        ];

        return $this->roleRepository->findById($id, $withRelation, $allowedFields, $allowedIncludes);
    }

    public function destroy(int $id): int
    {
        $data = [];

        $role = $this->roleRepository->get($id);

        data_set($data, 'model', $role);

        return $this->rolePipeline->destroy($data);
    }


    public function view(int $id, Request $request)
    {
        $withRelation = ['permissions'];
        $allowedFields = ['id', 'name'];
        $allowedIncludes = [
            'permissions'

        ];
        $request->has('with_trashed') ? $withTrashed = true : $withTrashed = false;

        return $this->roleRepository->findById($id, $withRelation, $allowedFields, $allowedIncludes, $withTrashed);
    }

}
