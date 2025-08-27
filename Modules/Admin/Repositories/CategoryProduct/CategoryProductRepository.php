<?php

namespace Admin\Repositories\CategoryProduct;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\CategoryProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CategoryProductRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(CategoryProduct $model)
    {
        parent::__construct($model);
    }

    private array $allowedFields = [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];

    public function getAllByFilters(
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        int   $perPage = null
    )
    {
        $defaultSort = 'id';
        $query = QueryBuilder::for($this->model)
            ->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($this->allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($defaultSort)
            ->allowedSorts($allowedSorts);

        return $perPage !== null
            ? $query->jsonPaginate($perPage)
            : $query->get();
    }

    public function create(array $Data): Model
    {
        json_encode($Data);
        return $this->model->create($Data);
    }

    public function update($model, array $Data): bool
    {
        return $model->update($Data);
    }

    public function get(int $id): Model
    {
        return QueryBuilder::for(CategoryProduct::class)
            ->allowedFields($this->allowedFields)
            ->allowedFilters([
                'name'
            ])
            ->withTrashed()
            ->findOrFail($id);
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function deleteCategory(int $id): mixed
    {
        try {
            Product::query()
                ->where('category_product_id', $id)
                ->withTrashed()
                ->orderBy('id')
                ->forceDelete();

            return $this->model
                ->query()
                ->findOrFail($id)
                ->forceDelete();
        } catch (QueryException|Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function archive(int $id): mixed
    {
        try {
            $query = QueryBuilder::for($this->model);
            return $query
                ->findOrFail($id)
                ->delete();
        } catch (QueryException|Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
