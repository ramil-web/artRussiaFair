<?php
/** @noinspection PhpParamsInspection */

namespace App\Repositories;

use BenSampo\Enum\Enum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class BaseRepository implements BaseRepositoriesInterface
{
    const DESC = 'desc';
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }


    public function getAllByFilters(
        array $role = [],
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
        count($role) !== 0 ? $query = $query->role($role) : $query;
        $query = $query->with($withRelation);
        $query = $query->allowedFilters($allowedFilters);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        $query = $query->defaultSort('id')
            ->allowedSorts($allowedSorts);

        $withTrashed ? $query = $query->withTrashed() : $query;

        return $perPage !== null & $query->count() > $perPage ? $query->jsonPaginate($perPage) : $query->get();
    }

    public function findById(
        int   $modelId,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        bool  $withTrashed = false
    ): ?Model
    {
        $query = QueryBuilder::for($this->model);
        $query = $query->with($withRelation);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        return $withTrashed ? $query->withTrashed()->findOfFail($modelId) : $query->findOrFail($modelId);
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
     * @param array $category
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
        int    $page = null,
        array  $category = []
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
            ->when($type !== null, fn($q) => $q->where('type', $type))
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($order . $sortBy);

        if (!empty($category) && $category[0] !== null) {
            $query->whereIn('type', $category);
        }

        return $perPage !== null
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }
}
