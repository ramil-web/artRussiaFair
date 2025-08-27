<?php

namespace Admin\Repositories\Event;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\Event;
use App\Models\TimeSlotStart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DbCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class EventRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function getAllByFilters(
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        int   $perPage = null
    )
    {
        $query = QueryBuilder::for($this->model);
        $query = $query->with($withRelation);
        $query = $query->allowedFilters($allowedFilters);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        $query = $query->defaultSort('order_column')
            ->allowedSorts($allowedSorts);

        return $perPage !== null
            ? $query->jsonPaginate($perPage)
            : $query->get();
    }

    /**
     * @param array $Data
     * @return Model
     * @throws CustomException
     */
    public function create(array $Data): Model
    {
        try {
            return $this->model
                ->findOrCreate($Data);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return DbCollection|array
     * @throws CustomException
     */
    public function slots(int $id): DbCollection|array
    {
        try {
            $query = TimeSlotStart::query()
                ->where([
                    'event_id' => $id,
                    'status'   => true
                ]);
            if (!$query->exists()) {
                return [];
            }
            return $query->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    private function jsonEncode(array &$Data): void
    {
        if (array_key_exists('place', $Data)) {
            $Data['place'] = json_encode($Data['place']);
        }
        if (array_key_exists('name', $Data)) {
            $Data['name'] = json_encode($Data['name']);
        }
        if (array_key_exists('description', $Data)) {
            $Data['description'] = json_encode($Data['description']);
        }
        if (array_key_exists('social_links', $Data)) {
            $Data['social_links'] = json_encode($Data['social_links']);
        }
    }

    public function updateEvent(Model $model, array $Data): Builder|array|DbCollection|Model
    {
        return \Synergy\Events\Event::updateEvent($model, $Data);
    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function delete(int $id): mixed
    {
        try {
            $query = QueryBuilder::for($this->model);
            return $query
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
    public function archive(int $id): mixed
    {
        try {
            $query = QueryBuilder::for($this->model);
            return $query
                ->findOrFail($id)
                ->delete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function findByName(
        string $name,
        array  $withRelation = [],
        array  $allowedFields = [],
        array  $allowedIncludes = [],
    ): ?Model
    {
        $query = QueryBuilder::for($this->model);
        $query = $query->with($withRelation);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        return $query
            ->whereJsonContains('name->ru', $name)
            ->orWhereJsonContains('name->en', $name)
            ->firstOrFail();
    }
}
