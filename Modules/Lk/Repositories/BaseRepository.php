<?php
/** @noinspection PhpParamsInspection */

namespace Lk\Repositories;

use App\Enums\AppStatusEnum;
use App\Enums\OrderItemTypesEnum;
use App\Exceptions\CustomException;
use App\Models\UserApplication;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BaseRepository implements BaseRepositoriesInterface
{
    const DESC = 'desc';
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }


    public function getAllByFilters(
        array $where = [],
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        bool  $withTrashed = false,
        int   $perPage = null
    )
    {
        $query = QueryBuilder::for($this->model);
        if (count($where) !== 0 && isset($where['name'])) {
            $query = $query->where($where['name'], $where['value']);
        }
        $query = $query->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort('id')
            ->allowedSorts($allowedSorts);

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $perPage !== null
            ? $query->jsonPaginate($perPage)
            : $query->get();
    }

    public function findById(
        int   $modelId,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        bool  $withTrashed = false
    ): ?Model
    {
        $query = QueryBuilder::for($this->model)
            ->with($withRelation)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes);

        return $withTrashed
            ? $query->withTrashed()->findOrFail($modelId)
            : $query->findOrFail($modelId);
    }

    public function create(array $Data): Model
    {
        return $this->model->create($Data);
    }

    public function update(Model $model, array $Data): bool
    {
        return $model->update($Data);
    }

    public function getSelf(): Model
    {
        // TODO: Implement getSelf() method.
    }

    public function updateSelf(): Model
    {
        // TODO: Implement updateSelf() method.
    }

    public function softDelete(Model $model): bool
    {
        return $model->delete();
    }

    public function restore(Model $model): bool
    {
        return $model->restore();
    }

    public function forceDelete(Model $model): bool
    {
        return $model->forceDelete();
    }

    public function findByIdForUserByType(
        OrderItemTypesEnum $type,
        int                $userId,
        int                $modelId,
        array              $withRelation = [],
        array              $allowedFields = [],
        array              $allowedIncludes = [],
        bool               $withTrashed = false
    ): ?Model
    {
        $query = QueryBuilder::for($this->model)
            ->with($withRelation)
            ->where('type', $type)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes);
        /**
         * Проверяем участинка по ID
         */
        $query->whereHas('user_applications', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
        return $withTrashed
            ? $query->withTrashed()->findOrFail($modelId)
            : $query->find($modelId);
    }

    /**
     * @param int $userId
     * @param int $id
     * @param array $withRelation
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param array $where
     * @return Collection|Model|QueryBuilder|QueryBuilder[]|null
     * @throws CustomException
     */
    public function findByIdForUser(
        int   $userId,
        int   $id,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $where = []
    )
    {
        try {
            $query = QueryBuilder::for($this->model)
                ->with($withRelation)
                ->allowedFields($allowedFields)
                ->allowedIncludes($allowedIncludes);
            $query->where($where);

            $this->checkUser($query, $userId);
            $this->whereUserApplicationConfirmed($query, $userId);

            return $query->findOrFail($id);
        } catch (\Illuminate\Database\QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @param array $where
     * @param array $withRelation
     * @param array $allowedFilters
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param array $allowedSorts
     * @param bool $withTrashed
     * @param int|null $perPage
     * @return Collection|QueryBuilder[]
     * @throws CustomException
     */
    public function getAllByFiltersForUser(
        array $where = [],
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        bool  $withTrashed = false,
        int   $perPage = null,
    )
    {
        try {
            $query = QueryBuilder::for($this->model);
            $query = $query->with($withRelation);

            /**
             * Если есть тип
             */
            if (array_key_exists('type', $where)) {
                $query = $query->where('type', $where['type']);
            }

            $query = $query->allowedFilters($allowedFilters)
                ->allowedFields($allowedFields)
                ->allowedIncludes($allowedIncludes)
                ->defaultSort('id')
                ->allowedSorts($allowedSorts);

            $query = $withTrashed ? $query->withTrashed() : $query;
            $userId = $where['user_id'];

            $this->checkUser($query, $userId);
            $this->whereUserApplicationConfirmed($query, $userId);

            return $perPage !== null
                ? $query->jsonPaginate($perPage)
                : $query->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param QueryBuilder $query
     * @param mixed $userId
     * @return void
     */
    private function checkUser(QueryBuilder $query, int $userId): void
    {
        $query->whereHas('users', function ($q) use ($userId) {
            $q->where('users.id', $userId);
        });
    }

    /**
     * @param QueryBuilder $query
     * @param int $userApplicationId
     * @return void
     */
    public function whereUserApplicationConfirmed(QueryBuilder $query, int $userApplicationId): void
    {
        $query->whereHas('user_applications', function ($q) {
            $q->where('status', AppStatusEnum::CONFIRMED());
        });
    }

    /**
     * @param int $userApplicationId
     * @return bool
     */
    public function checkUserAppStatus(int $userApplicationId): bool
    {
        $userApplication = UserApplication::query()->find($userApplicationId);
        return $userApplication->status == AppStatusEnum::CONFIRMED()->value;
    }

    public function getAllWithPaginate(
        int    $id,
        Model  $model,
        array  $allowedFilters,
        string $orderBy,
        int    $page = null,
        int    $perPage = null,
        array  $withRelation = [],
        array  $allowedFields = [],
        array  $allowedIncludes = [],
        string $sortBy = 'id'
    ): Collection|LengthAwarePaginator|array
    {
        $pageName = 'page';
        /**
         * Order by any column ASC & DESC
         */
        $order = strtolower($orderBy) == self::DESC ? '-' : '';
        $query = QueryBuilder::for($model)
            ->where('user_application_id', $id)
            ->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($order . $sortBy);

        return $perPage !== null
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }

}
