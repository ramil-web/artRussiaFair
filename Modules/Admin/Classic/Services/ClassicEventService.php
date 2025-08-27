<?php

namespace Admin\Classic\Services;

use Admin\Classic\Repositories\ClassicEventRepository;
use Admin\Http\Filters\NameFilter;
use App\Exceptions\CustomException;
use App\Models\ClassicEvent;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClassicEventService
{
    const DELETE = 'delete';
    const ARCHIVE = 'archive';

    public function __construct(
        public ClassicEventRepository $repository,
        public ClassicEvent           $classicEvent
    )
    {
    }

    /**
     * @param array $data
     * @return Model|null
     * @throws CustomException
     */
    public function create(array $data): ?Model
    {
        $event = $this->repository->create($data);
        return $this->repository->findById($event->id);
    }

    /**
     * @param array $data
     * @return Model|null
     * @throws CustomException
     */
    public function update(array $data): ?Model
    {
        $this->repository->updateEvent($data);
        return $this->repository->findById($data['id']);
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function show(int $id): ?Model
    {
        $withRelation = [];
        $allowedFields = [];
        $allowedIncludes = [];
        return $this->repository->findById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
        );
    }

    public function list(mixed $data)
    {
        $withRelation = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('type'),
            AllowedFilter::exact('status'),
            AllowedFilter::exact('year'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::trashed()
        ];
        $allowedFields = [
            'id', 'name', 'description', 'social_links', 'place',
            'year', 'start_date', 'end_date', 'status',
            'event_type'

        ];
        $allowedIncludes = [];
        $sortBy = array_key_exists('sort_by', $data) ? $data['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $data) ? $data['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $data) ? $data['per_page'] : null;
        $page = array_key_exists('page', $data) ? $data['page'] : null;

        return $this->repository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->classicEvent,
            $perPage,
            null,
            $page
        );
    }

    /**
     * @param int $id
     * @param string $delete
     * @return bool
     * @throws CustomException
     * @throws Throwable
     */
    public function delete(int $id, string $delete): bool
    {
        try {
            DB::beginTransaction();

            /**
             * Completely remove | softDelete
             */
            if ($delete == self::DELETE) {
                $deleted = $this->repository->delete($id);
            } else {
                $deleted = $this->repository->archive($id);
            }
            DB::commit();
            return $deleted;
        } catch (QueryException|Throwable $e) {
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
            return $this->classicEvent->onlyTrashed()->find($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param mixed $id
     * @return mixed
     * @throws CustomException
     */
    public function restore(mixed $id): mixed
    {
        try {
            $model = $this->checkData($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
