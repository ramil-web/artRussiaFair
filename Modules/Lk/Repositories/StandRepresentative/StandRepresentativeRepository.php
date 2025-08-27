<?php

namespace Lk\Repositories\StandRepresentative;

use App\Exceptions\CustomException;
use App\Models\StandRepresentative;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;

class StandRepresentativeRepository extends BaseRepository
{
    public function __construct(StandRepresentative $model)
    {
        parent::__construct($model);
    }

    /**
     * @throws CustomException
     */
    public function store(array $dataApp): Model|Builder
    {
        try {
            return $this->model->query()
                ->create($dataApp);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return Builder|Builder[]|Collection|Model|null
     * @throws CustomException
     */
    public function show(int $id): Model|Collection|Builder|array|null
    {
        try {
            return $this->model->query()
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function updateData(array $dataApp): Model
    {
        try {
            $this->update($this->findById($dataApp['id']), $dataApp);
            return $this->findById($dataApp['id']);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id): bool
    {
        try {
            return $this->forceDelete($this->findById($id));
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
