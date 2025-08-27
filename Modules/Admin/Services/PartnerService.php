<?php

namespace Admin\Services;

use Admin\Http\Filters\EventCategoryFilter;
use Admin\Http\Filters\NameFilter;
use Admin\Repositories\Partner\PartnerRepository;
use App\Exceptions\CustomException;
use App\Http\Filters\EventFilter;
use App\Models\Partner;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PartnerService
{

    const DELETE = 'delete';
    const ARCHIVE = 'archive';

    public function __construct(
        protected PartnerRepository        $partnerRepository,
        public Partner                   $partner,
        protected PartnerCategoryService $partnerCategoryService
    )
    {
    }

    /**
     * @throws Throwable
     * @throws CustomException
     */
    public function create(mixed $dataApp): ?Model
    {
        try {
            DB::beginTransaction();
            $model = $this->partnerRepository->create($dataApp);

            $this->partnerRepository->createEventGables($dataApp['event_id'], $model->id, Partner::class);
            $created = $this->partnerRepository->findById($model->id, ['eventgable']);
            unset($created->deleted_at);
            DB::commit();
            return $created;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function checkData(int $id): mixed
    {
        try {
            return $this->partner->onlyTrashed()->find($id);
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
            $model = $this->partner->onlyTrashed()->find($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param int $id
     * @param $data
     * @return Model|null
     */
    public function show(int $id, $data): ?Model
    {
        $withRelation = ['partnerCategory'];
        $allowedFields = [
            'name',
            'link',
            'partner_catalog_id',
            'image',
            'event_id',
        ];
        $allowedIncludes = [];
        $partner = $this->partnerRepository->findByIdAndTypeWithRelations(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $this->partner,
            null
        );
        if (!$partner) {
            return null;
        }

        $category = $data['filter']['category'] ?? null;

        if ($category) {
            $filteredEventgables = $partner->eventgable()
                ->whereHas('event', function ($query) use ($category) {
                    $query->where('category', $category);
                })
                ->get();

            $partner->setRelation('eventgable', $filteredEventgables);
        } else {
            $partner->load('eventgable');
        }

        return $partner;
    }

    /**
     * @throws CustomException
     */
    public function delete(int $id, string $delete)
    {
        try {
            DB::beginTransaction();

            /**
             * Completely remove | softDelete
             */
            if ($delete == self::DELETE) {
                $deleted = $this->partnerRepository->delete($id);
                $this->partnerRepository->deleteEvetgable($id);
            } else {
                $deleted = $this->partnerRepository->archive($id);
            }
            DB::commit();
            return $deleted;
        } catch (QueryException|Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws Throwable
     * @throws CustomException
     */
    public function update(int $id, mixed $dataApp): Model|array
    {
        try {
            DB::beginTransaction();
            $model = $this->partnerRepository->findById($id);

            if (!$model) {
                throw new CustomException("Ресурс с идентификатором $id не был найден.", Response::HTTP_BAD_REQUEST);
            }

            $this->partnerRepository->updatePartner($model->id, $dataApp);

            /**
             * Обновляем привязку к собитию.
             */
            if (array_key_exists('event_id', $dataApp)) {
                $this->partnerRepository->updateEventGables($dataApp['event_id'], $id);
            }
            $updated = $this->partnerRepository->findById($id, ['eventgable']);
            DB::commit();
            $category = $this->partnerCategoryService->show($updated->partner_category_id);
            $updated->partner_categort = $category;
            return $updated;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $dataApp
     * @return Collection|LengthAwarePaginator
     */
    public function list($dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = ['eventgable'];
        $allowedFields = [
            'id',
            'sort_id',
            'important',
            'name',
            'partner_category_id',
            'image',
            'link',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $allowedIncludes = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('partner_category_id'),
            AllowedFilter::exact('important'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::custom('event_id', new EventFilter()),
            AllowedFilter::custom('category', new EventCategoryFilter()),
            AllowedFilter::trashed(),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        $response = $this->partnerRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->partner,
            $perPage,
            null,
            $page
        );
        return $response;
    }


    public function archive(int $id): mixed
    {
        return QueryBuilder::for($this->partner)
            ->findOrFail($id)
            ->delete();
    }
}
