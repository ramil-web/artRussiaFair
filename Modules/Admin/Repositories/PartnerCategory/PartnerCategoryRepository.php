<?php

namespace Admin\Repositories\PartnerCategory;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\PartnerCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class PartnerCategoryRepository extends BaseRepository
{
    public function __construct(PartnerCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $Data
     * @return Model
     */
    public function create(array $Data): Model
    {
        return $this->model->create($Data);
    }

    /**
     * @param mixed $id
     * @param mixed $Data
     * @return true
     */
    public function updatePartnerCategory(mixed $id, mixed $Data): bool
    {
        $model = $this->model->withTrashed()->find($id);
        return $model->update($Data) && $model->trashed() ? $model->delete() : true;
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function archive(int $id): bool
    {
        try {
            $model = $this->model->query()->findOrFail($id);
            return $this->softDelete($model);
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
            return $this->model->query()->withTrashed()->find($id)->forceDelete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
