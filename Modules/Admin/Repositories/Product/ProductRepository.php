<?php

namespace Admin\Repositories\Product;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ProductRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    private array $allowedFields = [
        'id',
        'name',
        'description',
        'specifications',
        'price',
        'created_at',
        'updated_at'
    ];

    public function getAllByFilters(
        array  $withRelation = [],
        array  $allowedFilters = [],
        array  $allowedFields = [],
        array  $allowedIncludes = [],
        array  $allowedSorts = [],
        int    $perPage = null,
        string $sortBy = null,
        string $orderBy = null,
        int    $page = null
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

        $query = QueryBuilder::for($this->model)
            ->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($this->allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($order . $sortBy);

        return $perPage !== null
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }

    public function create(array $Data): Model
    {
        return $this->model->create($Data);
    }


    public function update($model, array $Data): bool
    {
        return $model->update($Data);
    }

    public function get(int $id): Model
    {
        return QueryBuilder::for(Product::class)
            ->allowedFields($this->allowedFields)
            ->withTrashed()
            ->findOrFail($id);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function find(int $id): mixed
    {
        try {
            return $this->model
                ->query()
                ->withTrashed()
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function deleteProduct(int $id): bool
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
     * @return bool
     * @throws CustomException
     */
    public function softDeleteProduct(int $id): bool
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
