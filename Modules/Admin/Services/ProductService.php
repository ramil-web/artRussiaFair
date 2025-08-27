<?php

namespace Admin\Services;

use Admin\Http\Filters\MultiLangFilter;
use Admin\Repositories\Product\ProductRepository;
use App\Exceptions\CustomException;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository, protected Product $product)
    {
        $this->productRepository = $productRepository;
    }

    public function create(array $data): Model
    {
        $model = $this->productRepository->create($data);

        return $this->productRepository->findById($model->id);
    }

    public function update(int $id, array $data): Model
    {
        $model = $this->productRepository->get($id);
        $this->productRepository->update($model, $data);

        return $model;
    }

    /**
     * @param int $id
     * @param string $delete
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id, string $delete): bool
    {
        try {
            /**
             * Completely remove | softDelete
             */
            if ($delete == ParticipantService::DELETE) {
              return  $this->productRepository->deleteProduct($id);
            } else {
              return  $this->productRepository->softDeleteProduct($id);
            }
        } catch (QueryException|Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return Collection|LengthAwarePaginator|array
     */
    public function list(array $dataApp): Collection|LengthAwarePaginator|array
    {
        $withRelation = ['categoryProduct'];
        $allowedFilters = [
            AllowedFilter::exact('category_product_id'),
            AllowedFilter::exact('article'),
            AllowedFilter::custom('name', new MultiLangFilter('name')),
            AllowedFilter::custom('specifications', new MultiLangFilter('specifications')),
            AllowedFilter::trashed(),
        ];

        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;
        $allowedFields = [
            'id', 'name','category_product_id', 'description','specifications',
            'price', 'article','sort_id','created_at','updated_at', 'deleted_at'
        ];

        $allowedIncludes = [
            'categoryProduct'
        ];
        return $this->productRepository->getAllByFilters(
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            [],
            $perPage,
            $sortBy,
            $orderBy,
            $page
        );
    }

    /**
     * @param int $id
     * @return Model|null
     * @throws CustomException
     */
    public function show(int $id): ?Model
    {
        return $this->productRepository->find($id);
    }

    /**
     * @param int $id
     * @return Builder|Builder[]|Collection|Model|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|mixed|null
     * @throws CustomException
     */
    public function checkData(int $id): mixed
    {
        try {
            return $this->product->onlyTrashed()->find($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function restore(int $id): mixed
    {
        try {
            $model = $this->product->onlyTrashed()->find($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
