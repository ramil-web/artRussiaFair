<?php

namespace Admin\Classic\Repositories;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\ClassicEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ClassicEventRepository extends BaseRepository
{
    public function __construct(ClassicEvent $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $Data
     * @return Model
     * @throws CustomException
     */
    public function create(array $Data): Model
    {
        try {
            return $this->model
                ->findOrCreate($Data);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $data
     * @return bool
     * @throws CustomException
     */
    public function updateEvent(array $data): bool
    {
        try {
            $model = $this->model->query()->find($data['id']);
            return $this->update($model, $data);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param mixed $id
     * @return mixed
     * @throws CustomException
     */
    public function delete(mixed $id): bool
    {
        try {
            $query = QueryBuilder::for($this->model);
            return $query
                ->withTrashed()
                ->find($id)
                ->forceDelete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param mixed $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function archive(mixed $id): mixed
    {
        try {
            $query = QueryBuilder::for($this->model);
            return $query
                ->findOrFail($id)
                ->delete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
