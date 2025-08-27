<?php

namespace Admin\Services;

use Admin\Http\Filters\MultiLangFilter;
use Admin\Repositories\ServiceCatalog\ServiceCatalogRepository;
use App\Exceptions\CustomException;
use App\Models\ServiceCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ServiceCatalogService
{
    private ServiceCatalogRepository $serviceCatalogRepository;

    public function __construct(
        ServiceCatalogRepository $serviceCatalogRepository,
        public ServiceCatalog    $model
    )
    {
        $this->serviceCatalogRepository = $serviceCatalogRepository;
    }

    public function create(array $data): Model
    {
        $model = $this->serviceCatalogRepository->create($data);
        return $this->show($model->id);
    }

    public function show(int $id): ?Model
    {
        $response = $this->serviceCatalogRepository->get($id);
        $this->jsonDecode($response);
        return $response;
    }


    /**
     * @param int $id
     * @param array $data
     * @return mixed
     * @throws CustomException
     */
    public function update(int $id, array $data): mixed
    {
        return $this->serviceCatalogRepository->updateData($id, $data);
    }

    /**
     * @throws Throwable
     */
    public function delete(int $id, $delete): bool
    {
        try {
            /**
             * Completely remove | softDelete
             */
            if ($delete == ParticipantService::DELETE) {
                return $this->serviceCatalogRepository->forceDeleteData($id);
            } else {
                return $this->serviceCatalogRepository->softDeleteData($id);
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
        $withRelation = [];
        $allowedFilters = [
            AllowedFilter::custom('name', new MultiLangFilter('name')),
            AllowedFilter::custom('category', new MultiLangFilter('category')),
            AllowedFilter::trashed(),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        $allowedFields = [
            'id', 'image', 'description', 'name', 'category', 'other', 'price', 'created_at', 'updated_at'
        ];


        $response = $this->serviceCatalogRepository->getDataAllByFilters(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedFields,
            $this->model,
            $perPage,
            $page,
        );
        foreach ($response as $item) {
            $this->jsonDecode($item);
        }
        return $response;
    }

    /**
     * @param Model|null $response
     * @return void
     */
    private function jsonDecode(?Model &$response): void
    {
        $jsonColumns = ['name', 'description', 'category', 'other'];
        foreach ($response as $key => $column) {
            if (in_array($column, $jsonColumns)) {
                $response[$key] = json_decode($column, true);
            }
        }
    }

    /**
     * @param int $id
     * @return Builder|Builder[]|Collection|Model|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|mixed|null
     * @throws CustomException
     */
    public function checkData(int $id): mixed
    {
        try {
            return $this->model->onlyTrashed()->find($id);
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
            $model = $this->model->onlyTrashed()->find($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
