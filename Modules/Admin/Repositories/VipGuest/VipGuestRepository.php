<?php

namespace Admin\Repositories\VipGuest;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\VipGuest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class VipGuestRepository extends BaseRepository
{
    public function __construct(VipGuest $model)
    {
        parent::__construct($model);
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

        return $withTrashed ? $query->withTrashed()->findOrFail($modelId) : $query->findOrFail($modelId);
    }

    /**
     * @param int $id
     * @return object|null
     * @throws CustomException
     */
    public function findGuestById(int $id): object|null
    {
        try {
            $model = $this->model->query()->findOrFail($id);
            $withRelation = ['userProfile', 'userApplication'];
            return $this->findById(
                $model->id,
                $withRelation
            );
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $withRelation
     * @param array $allowedFilters
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param mixed $perPage
     * @param mixed $page
     * @param mixed $sort
     * @return LengthAwarePaginator|Collection|QueryBuilder[]
     */
    public function getAllByFilterAndSorts(
        array $withRelation,
        array $allowedFilters,
        array $allowedFields,
        array $allowedIncludes,
        mixed $perPage,
        mixed $page,
        mixed $sort
    ): Collection|LengthAwarePaginator|array
    {
        $defaultSort = '-created_at';
        $pageName = 'page';
        $query = QueryBuilder::for($this->model);
        $query = $query->with($withRelation)
            ->select(
                'id',
                'user_application_id',
                'full_name',
                'organization',
                'email',
                'created_at',
                'updated_at'
            )
            ->allowedFilters($allowedFilters)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($defaultSort)
            ->allowedSorts($sort);
        return $perPage !== null ? $query->paginate($perPage, $allowedFields, $pageName, $page) : $query->get();
    }
}
