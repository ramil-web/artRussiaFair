<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Repositories\Vacancy\VacancyRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class VacancyService
{
    public function __construct(public VacancyRepository $repository)
    {
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
     * @return array|Collection
     * @throws CustomException
     */
    public function list(): Collection|array
    {
        return $this->repository->list();
    }
}
