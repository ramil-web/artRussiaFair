<?php

namespace Admin\Repositories\ServiceCatalog;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\ServiceCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ServiceCatalogRepository extends BaseRepository
{
    public function __construct(ServiceCatalog $model)
    {
        parent::__construct($model);
    }

    private array $allowedFields = [
        'id',
        'name',
        'image',
        'description',
        'category',
        'other',
        'price',
    ];

    public function getDataAllByFilters(
        string $sortBy,
        string $orderBy,
        array  $withRelation = [],
        array  $allowedFilters = [],
        array  $allowedFields = [],
        array  $allowedIncludes = [],
        Model  $model = null,
        int    $perPage = null,
        int    $page = null,
    ): Collection|LengthAwarePaginator|array
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
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($order . $sortBy);

        return $perPage !== null
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }

    public function create(array $Data): Model
    {
        $this->jsonEncode($Data);
        return $this->model->query()->create($Data);
    }

    /**
     * @param int $id
     * @param array $Data
     * @return mixed
     * @throws CustomException
     */
    public function updateData(int $id, array $Data): mixed
    {
        try {
            $this->jsonEncode($Data);
            $model = $this->model->withTrashed()->find($id);
            if ($model->update($Data) && $model->trashed()) {
                $model->delete();
            }
            return $model;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function get(int $id): Model
    {
        return QueryBuilder::for(ServiceCatalog::class)
            ->findOrFail($id);
    }

    /**
     * @param array $Data
     * @return void
     */
    private function jsonEncode(array &$Data): void
    {
        $jsonColumns = ['name', 'description', 'category', 'other'];
        foreach ($Data as $key => $column) {
            if (in_array($column, $jsonColumns)) {
                $Data[$key] = json_encode($column, JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function forceDeleteData(int $id): mixed
    {
        try {
            return $this->model
                ->query()
                ->withTrashed()
                ->findOrFail($id)
                ->forceDelete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function softDeleteData(int $id): mixed
    {
        try {
            return $this->model
                ->query()
                ->findOrFail($id)
                ->delete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
