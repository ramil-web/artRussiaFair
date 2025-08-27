<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\MyTeam\MyTeamRepository;

class MyTeamService
{
    public function __construct(public MyTeamRepository $repository)
    {
    }

    /**
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function checkMyTeam(int $userApplicationId): bool
    {
        return $this->repository->checkMyTeam($userApplicationId);
    }

    /**
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function store(array $dataApp): Model
    {
        return $this->repository->store($dataApp);
    }

    /**
     * @param int $userApplicationId
     * @return Builder|Model
     * @throws CustomException
     */
    public function show(int $userApplicationId): Model|Builder
    {
        return $this->repository->show($userApplicationId);
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


    /**
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function update(array $dataApp): Model
    {
        return $this->repository->updateTeam($dataApp);
    }

    /**
     * @param mixed $dataApp
     * @return bool
     * @throws CustomException
     */
    public function checkStandRepresentative( $dataApp): bool
    {
        return $this->repository->checkStandRepresentative($dataApp);
    }

    public function checkSlot(int $even)
    {
        return $this->repository->checkSlot();
    }
}
