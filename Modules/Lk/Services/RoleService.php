<?php

namespace Lk\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Lk\Http\Requests\Role\StoreRequest;
use Lk\Http\Requests\Role\UpdateRequest;
use Lk\Repositories\Role\RoleRepositoryInterface;

class RoleService
{
    /**
     * @var RoleRepositoryInterface
     */
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

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
