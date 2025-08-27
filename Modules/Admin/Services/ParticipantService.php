<?php

namespace Admin\Services;

use Admin\Http\Filters\EventCategoryFilter;
use Admin\Http\Filters\NameFilter;
use Admin\Repositories\Participant\ParticipantRepository;
use Admin\Services\Relation\RelationService;
use App\Enums\ParticipantTypesEnum;
use App\Exceptions\CustomException;
use App\Models\Event;
use App\Models\EventGable;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ParticipantService
{

    const DELETE = 'delete';
    const ARCHIVE = 'archive';

    public function __construct(
        public ParticipantRepository $participantRepository,
        public RelationService       $relationService,
        public Participant           $participant
    )
    {
    }

    /**
     * @throws Throwable
     * @throws CustomException
     */
    public function create(array $dataApp): ?Model
    {
        try {
            DB::beginTransaction();
            $model = $this->participantRepository->create($dataApp);

            $this->participantRepository->createEventGables($dataApp['event_id'], $model->id, Participant::class);
            $created = $this->participantRepository->findById($model->id, ['eventgable']);
            $this->jsonDecode($created);
            DB::commit();
            return $created;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param ParticipantTypesEnum $type
     * @return Model|null
     */
    public function show(int $id, ParticipantTypesEnum $type): ?Model
    {
        $withRelation = ['eventgable'];
        $allowedFields = [
            'name',
            'description',
            'image',
            'event_id',
        ];
        $allowedIncludes = [];

        $response = $this->participantRepository->findByIdAndTypeWithRelations(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $this->participant,
            $type
        );
        $this->jsonDecode($response);
        return $response;
    }

    /**
     * @param array $dataApp
     * @return Collection|LengthAwarePaginator
     */
    public function list(array $dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = [];
        $allowedFields = [
            'id',
            'slug',
            'sort_id',
            'name',
            'description',
            'image',
            'images',
            'stand_id',
            'created_at',
            'updated_at',
            'deleted_at'
        ];
        $allowedIncludes = [];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::custom('category', new EventCategoryFilter()),
            AllowedFilter::trashed(),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'sort_id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        $response = $this->participantRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->participant,
            $perPage,
            $dataApp['type'],
            $page
        );
        foreach ($response as $val) {
            $this->jsonDecode($val);
        }
        return $response;
    }

    /**
     * @param int $id
     * @param mixed $dataApp
     * @param ParticipantTypesEnum $type
     * @return Model|null
     * @throws CustomException
     * @throws Throwable
     */
    public function update(int $id, mixed $dataApp, ParticipantTypesEnum $type): ?Model
    {
        try {
            DB::beginTransaction();
            $model = $this->participantRepository->findByIdAndTypeParticipant($id, $type);

            if (!$model) {
                throw new CustomException("Ресурс с идентификатором $id не был найден.", Response::HTTP_BAD_REQUEST);
            }
            $this->participantRepository->updateParticipant($model->id, $dataApp);

            /**
             * Обновляем привязку к собитию.
             */
            if (array_key_exists('event_id', $dataApp)) {
                $this->updateEventGables($dataApp['event_id'], $id);
            }
            $updated = $this->participantRepository->findById($id, ['eventgable'], [], [], true);
            $this->jsonDecode($updated);
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
                        'eventgable_type' => Participant::class,
                        'eventgable_id' => $modelId,
                    ])
                    ->delete();
            }
            $this->participantRepository->createEventGables($eventIds, $modelId, Participant::class);
        } catch (Throwable|QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param ParticipantTypesEnum $type
     * @param string $delete
     * @return mixed
     * @throws CustomException
     */
    public function delete(int $id, ParticipantTypesEnum $type, string $delete): mixed
    {
        try {
            DB::beginTransaction();

            /**
             * Completely remove | softDelete
             */
            if ($delete == self::DELETE) {
                $deleted = $this->participantRepository->delete($id, $type);
                $this->participantRepository->deleteEvetgable($id);
            } else {
                $deleted = $this->participantRepository->archive($id, $type);
            }
            DB::commit();
            return $deleted;
        } catch (QueryException|Throwable $e) {
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
            return $this->participant->onlyTrashed()->find($id);
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
            $model = $this->participant->onlyTrashed()->find($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param Model|array $created
     * @return void
     */
    private function jsonDecode(Model|array &$created): void
    {
        $created['images'] = json_decode($created['images']);
        $created['name'] = json_decode($created['name']);
        $created['description'] = json_decode($created['description']);
    }

    /**
     * @param int $id
     * @param string $image
     * @return bool
     * @throws CustomException
     */
    public function deleteImage(int $id, string $image): bool
    {
        return $this->participantRepository->deleteImage($id, $image);
    }
}
