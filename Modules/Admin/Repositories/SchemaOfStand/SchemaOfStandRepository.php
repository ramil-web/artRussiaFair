<?php

namespace Admin\Repositories\SchemaOfStand;

use App\Exceptions\CustomException;
use App\Models\SchemaOfStand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SchemaOfStandRepository extends BaseRepository
{
    public function __construct(SchemaOfStand $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $appData
     * @return Model|Builder
     * @throws CustomException
     */
    public function store(array $appData): Model|Builder
    {
        try {
            if ($this->model->query()->exists()) {
                throw new CustomException(
                    "Чтобы добавить новую схему надо удалить существующий",
                    Response::HTTP_BAD_REQUEST
                );
            }
            return $this->model->query()->firstOrCreate($appData);
        } catch (Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function deleteSchema(int $id): bool
    {
        try {
            $model = $this->model->query()->findOrfail($id);
            return $this->forceDelete($model);
        } catch (Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(): Model|Collection|Builder|array|null
    {
        try {
            return $this->model->query()->firstOrFail();
        } catch (Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
