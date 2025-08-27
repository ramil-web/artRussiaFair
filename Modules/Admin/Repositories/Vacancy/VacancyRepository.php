<?php

namespace Admin\Repositories\Vacancy;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\Vacancy;
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
     * @param array $dataApp
     * @return mixed
     * @throws CustomException
     */
    public function createVacancy(array $dataApp): mixed
    {
        try {
            return $this->create($dataApp);
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
            return $this->model->query()->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        try {
            return $this->model->query()->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function deleteVacancy(int $id): mixed
    {
        try {
            return $this->model->query()->find($id)->delete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return array|Builder|Collection|Model
     * @throws CustomException
     */
    public function updateVacancy(array $dataApp): array|Builder|Collection|Model
    {
        try {
            $model = $this->show($dataApp['id']);
            if ($this->update($model, $dataApp)) {
                return $this->show($dataApp['id']);
            }
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
