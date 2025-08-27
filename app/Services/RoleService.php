<?php

namespace App\Services;

use admin\Http\Requests\Role\StoreRequest;
use admin\Http\Requests\Role\UpdateRequest;
use admin\Repositories\Role\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RoleService
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository
    )
    {}

    public function store(StoreRequest $request): Model
    {
        $data = data_get($request->validated(), 'data');

        return $this->roleRepository->store($data);
    }

    public function update(UpdateRequest $request, int $id): Model
    {
        $data = data_get($request->validated(), 'data');

        $role = $this->roleRepository->get($id);

        data_set($data, 'model', $role);

        return $this->roleRepository->update($data);
    }

    public function destroy(int $id): int
    {
        $data = [];

        $role = $this->roleRepository->get($id);

        data_set($data, 'model', $role);

        return $this->rolePipeline->destroy($data);
    }

    public function index(): Collection
    {
        return $this->roleRepository->index();
    }

    public function show(int $id): Model
    {
        return $this->roleRepository->get($id);
    }
}
