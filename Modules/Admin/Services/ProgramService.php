<?php

namespace Admin\Services;

use Admin\Http\Filters\EventTypeFilter;
use Admin\Http\Filters\ModeratorNameFilter;
use Admin\Http\Filters\NameFilter;
use Admin\Http\Filters\NewEventCategoryFilter;
use Admin\Http\Filters\SpeakerIdFilter;
use Admin\Repositories\Program\ProgramRepository;
use App\Exceptions\CustomException;
use App\Models\Program;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProgramService
{
    public function __construct(
        public ProgramRepository $programRepository,
        public Program           $program
    )
    {
    }

    /**
     * @param mixed $dataApp
     * @return Model|null
     * @throws CustomException|Throwable
     */
    public function create(mixed $dataApp): ?Model
    {
        try {
            DB::beginTransaction();
            $withRelation = ['event', 'speaker', 'partner'];
            $model = $this->programRepository->creatProgram($dataApp);
            if (!empty($dataApp['speaker_id'])) {
                $this->programRepository->createProgramSpeaker($dataApp, $model->id);
            }

            if (!empty($dataApp['partners_id'])) {
                $this->programRepository->createProgramPartner($dataApp, $model->id);
            }
            $response = $this->programRepository->findById($model->id, $withRelation);
            DB::commit();
            return $response;
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function show(int $id): ?Model
    {
        $withRelation = ['event', 'speaker', 'partner'];
        $allowedFields = [
            'id',
            'speaker_id',
            'start_time',
            'end_time',
            'name',
            'event_id',
            'moderator_name',
            'moderator_description',
            'program_format'
        ];
        $allowedIncludes = ['event', 'speaker', 'partner'];

        return $this->programRepository->findById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes
        );
    }

    /**
     * @param mixed $dataApp
     * @return Collection|LengthAwarePaginator
     */
    public function list(mixed $dataApp): Collection|LengthAwarePaginator
    {
        $withRelation = ['speaker', 'partner', 'eventType'];
        $allowedFields = [
            'id',
            'event_id',
            'start_time',
            'end_time',
            'date',
            'name',
            'moderator_name',
            'moderator_description',
            'program_format',
            'description',
            'created_at',
            'updated_at'
        ];
        $allowedIncludes = ['speaker', 'partner', 'eventType'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::custom('event_type', new EventTypeFilter()),
            AllowedFilter::exact('program_format'),
            AllowedFilter::custom('name', new NameFilter()),
            AllowedFilter::custom('moderator_name', new ModeratorNameFilter()),
            AllowedFilter::custom('category', new NewEventCategoryFilter()),
            AllowedFilter::custom('speaker_id', new SpeakerIdFilter()),
            AllowedFilter::trashed(),
        ];
        $sortBy = array_key_exists('sort_by', $dataApp) ? $dataApp['sort_by'] : 'id';
        $orderBy = array_key_exists('order_by', $dataApp) ? $dataApp['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] : null;
        $page = array_key_exists('page', $dataApp) ? $dataApp['page'] : null;

        return $this->programRepository->getAllByFiltersAndType(
            $sortBy,
            $orderBy,
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->program,
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
            $withRelation = ['event', 'speaker', 'partner'];
            $model = $this->programRepository->findById($id);
            if (!$model) {
                throw new CustomException("Ресурс с идентификатором $id не был найден.", Response::HTTP_BAD_REQUEST);
            }
            $this->programRepository->updateProgram($model->id, $dataApp);
            $this->programRepository->updateProgramSpeaker($model->id, $dataApp);
            $this->programRepository->updateProgramPartner($model->id, $dataApp);
            $response = $this->programRepository->findById($id, $withRelation);
            return $response;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @param string $delete
     * @return mixed
     * @throws CustomException
     */
    public function delete(int $id, string $delete): mixed
    {
        try {
            if ($delete == ParticipantService::DELETE) {
                $model = $this->programRepository->findById($id);
                return $model->forceDelete();
            } else {
                return $this->programRepository->archive($id);
            }
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
            return $this->program->onlyTrashed()->find($id);
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
            $model = $this->program->onlyTrashed()->find($id);
            return $model->restore();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    private function jsonDecode(Model $model)
    {
        $column = 'speaker_id';
        return $model->$column = json_decode($model->$column);
    }

    /**
     * @param Model|null $response
     * @return void
     */
    private function jsonDecodeEvent(?Model &$response): void
    {
        $columns = ['name', 'description', 'place', 'social_links'];
        foreach ($columns as $column) {
            $response->event->$column = json_decode($response->event->$column);
        }
    }
}
