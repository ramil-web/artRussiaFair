<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\StandRepresentative\StandRepresentativeRepository;

class StandRepresentativeService
{
    public function __construct(protected StandRepresentativeRepository $repository)
    {
    }

    /**
     * @param array $dataApp
     * @return Model|Builder
     * @throws CustomException
     */
    public function store(array $dataApp): Model|Builder
    {
        return $this->repository->store($dataApp);
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        return $this->repository->show($id);
    }

    /**
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function update(array $dataApp): Model
    {
        return $this->repository->updateData($dataApp);
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
