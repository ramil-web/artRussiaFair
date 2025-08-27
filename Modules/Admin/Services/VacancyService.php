<?php

namespace Admin\Services;

use Admin\Repositories\Vacancy\VacancyRepository;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class VacancyService
{
    public function __construct(public VacancyRepository $repository)
    {
    }

    /**
     * @param array $dataApp
     * @return mixed
     * @throws CustomException
     */
    public function create(array $dataApp): mixed
    {
        return $this->repository->createVacancy($dataApp);
    }

    /**
     * @return array|Collection
     * @throws CustomException
     */
    public function list(): Collection|array
    {
        return $this->repository->list();
    }

    /**
     * @param int $id
     * @return array|Builder|Collection|Model|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        return $this->repository->show($id);
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function delete(int $id): mixed
    {
        return $this->repository->deleteVacancy($id);
    }

    /**
     * @param array $dataApp
     * @return Model|Collection|Builder|array
     * @throws CustomException
     */
    public function update(array $dataApp): Model|Collection|Builder|array
    {
        return $this->repository->updateVacancy($dataApp);
    }
}
