<?php

namespace Admin\Services;

use Admin\Http\Filters\NameFilter;
use Admin\Repositories\PartnerCategory\PartnerCategoryRepository;
use App\Exceptions\CustomException;
use App\Models\PartnerCategory;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PartnerCategoryService
{

    public function __construct(
        protected PartnerCategoryRepository $partnerCategoryRepository,
        public PartnerCategory              $partnerCategory
    )
    {
    }

    /**
     * @param mixed $dataApp
     * @return Model
     * @throws CustomException
     */
    public function create(mixed $dataApp): Model
    {
        try {
            $model = $this->partnerCategoryRepository->create($dataApp);
            return $this->partnerCategoryRepository->findById($model->id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return Model
     */
    public function show(int $id): Model
    {
        $withRelation = [];
        $allowedFields = [
            'name',
            'id',
        ];
        $allowedIncludes = [];

        $response = $this->partnerCategoryRepository->findById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes
        );
        return $response;
    }

    public function list(mixed $dataApp): Collection|\Illuminate\Pagination\LengthAwarePaginator
    {
        $withRelation = [];
        $allowedFields = ['id', 'name', 'created_at', 'updated_at', 'sort_id'];
        $allowedIncludes = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::trashed(),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->partnerCategoryRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->partnerCategory,
            $perPage,
            null,
            $page
        );
    }

    /**
     * @param int $id
     * @param mixed $dataApp
     * @return Model|null
     * @throws CustomException
     */
    public function update(int $id, mixed $dataApp): ?Model
    {
        try {
            $model = $this->partnerCategoryRepository->findById($id);

            if (!$model) {
                throw new CustomException("Ресурс с идентификатором $id не был найден.", Response::HTTP_BAD_REQUEST);
            }
            $this->partnerCategoryRepository->updatePartnerCategory($model->id, $dataApp);
            return $this->partnerCategoryRepository->findById($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
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
            DB::beginTransaction();

            /**
             * Completely remove | softDelete
             */
            if ($delete == PartnerService::DELETE) {
               $deleted = $this->partnerCategoryRepository->delete($id);
            } else {
                $deleted = $this->partnerCategoryRepository->archive($id);
            }
            DB::commit();
            return $deleted;
        } catch (QueryException|Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
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
            return $this->partnerCategory->onlyTrashed()->find($id);
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
            $model = $this->partnerCategory->onlyTrashed()->find($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
