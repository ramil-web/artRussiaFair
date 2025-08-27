<?php

namespace Admin\Services;

use Admin\Http\Filters\EventCategoryFilter;
use Admin\Http\Filters\NameFilter;
use Admin\Repositories\Speaker\SpeakerRepository;
use App\Enums\SpeakerTypesEnum;
use App\Exceptions\CustomException;
use App\Models\Event;
use App\Models\EventGable;
use App\Models\Speaker;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SpeakerService
{

    const DELETE = 'delete';
    const ARCHIVE = 'archive';

    public function __construct(
        public SpeakerRepository $speakerRepository,
        public Speaker           $speaker
    )
    {
    }

    /**
     * @param mixed $dataApp
     * @return Model
     * @throws CustomException|Throwable
     */
    public function create(mixed $dataApp): Model
    {
        try {
            DB::beginTransaction();
            $model = $this->speakerRepository->create($dataApp);

            /**
             * Если при добавлени спикера, сразу привязываем к собитию.
             */
            if (array_key_exists('event_id', $dataApp)) {
                $this->speakerRepository->createEventGables($dataApp['event_id'], $model->id, Speaker::class);
            }
            $created = $this->speakerRepository->findById($model->id, ['eventgable']);
            DB::commit();
            return $created;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param SpeakerTypesEnum $type
     * @param array $data
     * @return Model|null
     */
    public function show(int $id, SpeakerTypesEnum $type, array $data = []): ?Model
    {
        $withRelation = [];
        $allowedFields = [
            'name',
            'description',
            'full_description',
            'image',
            'event_id',
            'position'
        ];
        $allowedIncludes = [];

        $speaker = $this->speakerRepository->findByIdAndTypeWithRelations(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $this->speaker,
            $type
        );

        if (!$speaker) {
            return null;
        }

        $category = $data['filter']['category'] ?? null;

        if ($category) {
            $filteredEventgables = $speaker->eventgable()
                ->whereHas('event', function ($query) use ($category) {
                    $query->where('category', $category);
                })
                ->get();

            $speaker->setRelation('eventgable', $filteredEventgables);
        } else {
            $speaker->load('eventgable');
        }

        return $speaker;
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
            AllowedFilter::custom('category', new EventCategoryFilter()),
            AllowedFilter::trashed(),
        ];
        $allowedFields = ['id', 'sort_id', 'name', 'description', 'full_description', 'image', 'created_at', 'updated_at', 'deleted_at'];
        $allowedIncludes = [];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->speakerRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->speaker,
            $perPage,
            $dataApp['type'],
            $page
        );
    }

    /**
     * @param int $id
     * @param mixed $dataApp
     * @param SpeakerTypesEnum $type
     * @return Model|null
     * @throws CustomException
     * @throws Throwable
     */
    public function update(int $id, mixed $dataApp, SpeakerTypesEnum $type): ?Model
    {
        try {
            DB::beginTransaction();
            $model = $this->speakerRepository->findByIdAndTypeSpeaker($id, $type);

            if (!$model) {
                throw new CustomException("Ресурс с идентификатором $id не был найден.", Response::HTTP_BAD_REQUEST);
            }

            $this->speakerRepository->updateSpeaker($model->id, $dataApp);
            $this->updateEventGables($dataApp['event_id'], $id);
            $updated = $this->speakerRepository->findById($id, ['eventgable'], [], [], true);
            DB::commit();
            return $updated;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $eventIds
     * @param int $modelId
     * @return void
     * @throws CustomException
     * Обнавляем привязку к сабытие
     */
    private function updateEventGables(array $eventIds, int $modelId): void
    {
        try {
            foreach ($eventIds as $eventId) {
                Event::query()->findOrFail($eventId);
                EventGable::query()
                    ->where([
                        'eventgable_type' => Speaker::class,
                        'eventgable_id'   => $modelId,
                    ])
                    ->delete();
            }
            $this->speakerRepository->createEventGables($eventIds, $modelId, Speaker::class);
        } catch (Throwable|QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param SpeakerTypesEnum $type
     * @param string $delete
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id, SpeakerTypesEnum $type, string $delete): bool
    {
        try {
            DB::beginTransaction();

            /**
             * Completely remove | softDelete
             */
            if ($delete == self::DELETE) {
                $deleted = $this->speakerRepository->delete($id, $type);
                $this->speakerRepository->deleteEvetgable($id);
            } else {
                $deleted = $this->speakerRepository->archive($id, $type);
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
            return $this->speaker->onlyTrashed()->find($id);
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
            $model = $this->speaker->onlyTrashed()->find($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
