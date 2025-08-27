<?php

namespace Admin\Repositories\Partner;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\Event;
use App\Models\EventGable;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PartnerRepository extends BaseRepository
{
    public function __construct(Partner $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $Data
     * @return Model
     */
    public function create(array $Data): Model
    {
        return $this->model->create($Data);
    }

    /**
     * @param mixed $id
     * @param mixed $Data
     * @return true
     */
    public function updatePartner(mixed $id, mixed $Data): bool
    {
        $model = $this->model->withTrashed()->find($id);
        return $model->update($Data) && $model->trashed() ? $model->delete() : true;
    }

    /**
     * @param array $eventIds
     * @param int $modelId
     * @return void
     * @throws CustomException
     * Обнавляем привязку к сабытие
     */
    public function updateEventGables(array $eventIds, int $modelId): void
    {
        try {
            foreach ($eventIds as $eventId) {
                Event::query()->findOrFail($eventId);
                EventGable::query()
                    ->where([
                        'eventgable_type' => Partner::class,
                        'eventgable_id'   => $modelId,
                    ])
                    ->delete();
            }
            $this->createEventGables($eventIds, $modelId, Partner::class);
        } catch (Throwable|QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool|mixed|null
     * @throws CustomException
     */
    public function delete(int $id): mixed
    {
        try {
            return QueryBuilder::for($this->model)
                ->withTrashed()
                ->findOrFail($id)
                ->forceDelete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @param int $id
     * @return mixed
     * @throws CustomException
     */
    public function archive(int $id): mixed
    {
        try {
            return QueryBuilder::for($this->model)
                ->findOrFail($id)
                ->delete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $modelId
     * @param array $withRelation
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param bool $withTrashed
     * @return Model|null
     */
    public function findById(
        int   $modelId,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        bool  $withTrashed = false
    ): ?Model
    {
        return QueryBuilder::for($this->model)
            ->with($withRelation)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->withTrashed()
            ->findOrFail($modelId);
    }
}
