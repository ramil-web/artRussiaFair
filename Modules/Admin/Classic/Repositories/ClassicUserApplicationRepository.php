<?php

namespace Admin\Classic\Repositories;

use Admin\Repositories\BaseRepository;
use App\Models\ClassicUserApplication;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;

class ClassicUserApplicationRepository extends BaseRepository
{
    public function __construct(ClassicUserApplication $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $sort
     * @param array $withRelation
     * @param array $allowedFields
     * @param array $allowedFilters
     * @param array $allowedIncludes
     * @param Model|null $model
     * @param int|null $perPage
     * @param int|null $page
     * @return Collection|LengthAwarePaginator
     */
    public function getUserApplications(
        string $sort,
        array  $withRelation = [],
        array  $allowedFields = [],
        array  $allowedFilters = [],
        array  $allowedIncludes = [],
        Model  $model = null,
        int    $perPage = null,
        int    $page = null,
    ): Collection|LengthAwarePaginator
    {
        $defaultSort = '-created_at';
        $pageName = 'page';

        /**
         * Order by any column ASC & DESC
         */
        if (in_array($sort, ['organization', '-organization', 'full_name', '-full_name'])) {
            $sort = $sort . '->' . app()->getLocale();
        }
        $query = QueryBuilder::for($model);
        $query = $query->with($withRelation)
            ->select(
                'id',
                'type',
                'name_gallery',
                'representative_surname',
                'representative_email',
                'representative_phone',
                'status',
                'active',
                'created_at',
                'updated_at',
                'visitor'
            )
            ->allowedFilters($allowedFilters)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($defaultSort)
            ->allowedSorts([$sort]);
        return $perPage !== null & $query->count() > $perPage
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }

    /**
     * @param mixed $id
     * @param array $withRelation
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @return Model|Collection|QueryBuilder|array|null
     */
    public function findUserAppById(
        mixed $id,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
    ): Model|Collection|QueryBuilder|array|null
    {
        $query = QueryBuilder::for($this->model);
        $query = $query->with($withRelation);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        return $query->findOrFail($id);
    }
}
