<?php

namespace App\Repositories\Vacancy;

use App\Exceptions\CustomException;
use App\Models\Vacancy;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;


class VacancyRepository extends BaseRepository
{
    public function __construct(Vacancy $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        try {
            return $this->model->query()
                ->where('status', true)
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return Collection|array
     * @throws CustomException
     */
    public function list(): Collection|array
    {
        try {
            return $this->model->query()
                ->where('status', true)
                ->orderBy('id')
                ->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
