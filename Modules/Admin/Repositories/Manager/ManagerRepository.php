<?php

namespace Admin\Repositories\Manager;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;

class ManagerRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getUserByFilters(
        array $role = [],
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        int   $perPage = null
    )
    {
        $query = QueryBuilder::for($this->model);

        if (!empty($role)) {
            $query->role($role);
        }

        $query
            ->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort('id')
            ->allowedSorts($allowedSorts);

        return $perPage !== null
            ? $query->simplePaginate($perPage)
            : $query->get();
    }


    public function update(Model $model, array $Data): bool
    {

        return $model->update($Data);
    }

    /**
     * @throws CustomException
     */
    public function delete(int $id): bool
    {
        try {
            $user = $this->model->query()->withTrashed()->find($id);
            return $user->forceDelete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function store(int $id): mixed
    {
        try {
            $user = $this->model->query()->find($id);
            return $user->delete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }
}
