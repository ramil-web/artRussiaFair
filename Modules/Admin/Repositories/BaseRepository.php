<?php

namespace Admin\Repositories;

use App\Exceptions\CustomException;
use App\Models\Event;
use App\Models\EventGable;
use BenSampo\Enum\Enum;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
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
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        int   $perPage = null
    )
    {
        $query = QueryBuilder::for($this->model)
            ->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort('id')
            ->allowedSorts($allowedSorts);

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
        return QueryBuilder::for($this->model)
            ->with($withRelation)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->withTrashed()
            ->findOrFail($modelId);
    }

    public function create(array $Data)
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
        return $model->withTrashed()->forceDelete();
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
    public function getAllByFilterAndType(
        array $where = [],
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        bool  $withTrashed = false,
        int   $perPage = null,
    ): Collection|QueryBuilder
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

            return $perPage !== null
                ? $query->jsonPaginate($perPage)
                : $query->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $modelId
     * @param array $withRelation
     * @param array $where
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param bool $withTrashed
     * @return Model|null
     */
    public function findByIdAndType(
        int   $modelId,
        array $withRelation = [],
        array $where = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        bool  $withTrashed = false
    ): ?Model
    {
        $query = QueryBuilder::for($this->model)
            ->with($withRelation)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->where($where);
        return $withTrashed ? $query->withTrashed()->findOrFail($modelId) : $query->findOrFail($modelId);
    }

    /**
     * @throws CustomException
     */
    public function createEventGables(array $eventIds, int $modelId, $model): void
    {
        try {
            $data = [];
            foreach ($eventIds as $eventId) {
                Event::query()->findOrFail($eventId);
                $data[] = [
                    'event_id'        => $eventId,
                    'eventgable_id'   => $modelId,
                    'eventgable_type' => $model,
                ];
            }
            EventGable::query()
                ->insert($data);
        } catch (\Illuminate\Database\QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $model
     * @return string
     */
    public function getModelPath(string $model): string
    {
        $modelName = Str::studly(Str::singular($model));
        return "App\\Models\\{$modelName}";
    }

    /**
     * @param int $modelId
     * @param array $withRelation
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param Model|null $model
     * @param Enum|null $type
     * @return Model|null
     */
    public function findByIdAndTypeWithRelations(
        int   $modelId,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        Model $model = null,
        Enum  $type = null,
    ): ?Model
    {
        return QueryBuilder::for($model)
            ->with($withRelation)
            ->when(!is_null($type), fn($q) => $q->where('type', $type))
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->withTrashed()
            ->findOrFail($modelId);
    }

    /**
     * @param string $sortBy
     * @param string $orderBy
     * @param array $withRelation
     * @param array $allowedFilters
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param Model|null $model
     * @param int|null $perPage
     * @param Enum|null $type
     * @param int|null $page
     * @return Collection|LengthAwarePaginator
     */
    public function getAllByFiltersAndType(
        string $sortBy,
        string $orderBy,
        array  $withRelation = [],
        array  $allowedFilters = [],
        array  $allowedFields = [],
        array  $allowedIncludes = [],
        Model  $model = null,
        int    $perPage = null,
        Enum   $type = null,
        int    $page = null
    ): Collection|LengthAwarePaginator
    {
        $pageName = 'page';
        /**
         * Order by any column ASC & DESC
         */
        $order = strtolower($orderBy) == self::DESC ? '-' : '';
        if ($sortBy == 'name' || $sortBy == 'description') {
            $sortBy = $sortBy . '->' . app()->getLocale();
        }

        $query = QueryBuilder::for($model)
            ->with($withRelation)
            ->when(!is_null($type), fn($q) => $q->where('type', $type))
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes);

        if ($sortBy != 'visualization') {
            $query = $query->defaultSort($order . $sortBy);
        }

        return $perPage !== null
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }

    /**
     * @param int $id
     * @return void
     * @throws CustomException
     */
    public function deleteEvetgable(int $id): void
    {
        try {
            EventGable::query()
                ->where('eventgable_id', $id)
                ->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
