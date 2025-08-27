<?php

namespace Admin\Services;

use Admin\Http\Filters\NameFilter;
use Admin\Repositories\CategoryProduct\CategoryProductRepository;
use App\Exceptions\CustomException;
use App\Models\CategoryProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CategoryProductService
{

    public function __construct(
        protected CategoryProductRepository $categoryProductRepository,
        protected CategoryProduct           $model
    ) {}

    public function create(array $data): Model
    {
        $model = $this->categoryProductRepository->create($data);
        return $this->categoryProductRepository->findById($model->id);
    }

    public function update(int $id, array $data): Model
    {
        $model = $this->categoryProductRepository->get($id);
        $this->categoryProductRepository->update($model, $data);
        return $model;
    }

    /**
     * @throws CustomException
     */
    public function delete(int $id, string $delete)
    {
        try {
            /**
             * Completely remove | softDelete
             */
            if ($delete == ParticipantService::DELETE) {
                return $this->categoryProductRepository->deleteCategory($id);
            } else {
                return $this->categoryProductRepository->archive($id);
            }
        } catch (QueryException|Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @param array $dataApp
     * @return Collection|LengthAwarePaginator
     */
    public function list(array $dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::trashed(),
        ];
        $allowedFields = ['id', 'sort_id', 'name', 'created_at', 'updated_at', 'deleted_at'];
        $allowedIncludes = [];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->categoryProductRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->model,
            $perPage,
            null,
            $page
        );
    }

    public function show(int $id): ?Model
    {
        return $this->categoryProductRepository->get($id);
    }

    /**
     * @throws CustomException
     */
    public function checkData(int $id)
    {

        try {
            return $this->model->onlyTrashed()->find($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @throws CustomException
     */
    public function restore(int $id)
    {
        try {
            $model = $this->model->onlyTrashed()->find($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
