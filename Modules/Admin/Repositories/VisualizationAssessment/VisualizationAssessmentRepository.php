<?php

namespace Admin\Repositories\VisualizationAssessment;

use Admin\Repositories\BaseRepository;
use Admin\Services\AssessmentService;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\VisualizationAssessment;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

;

class VisualizationAssessmentRepository extends BaseRepository
{
    public function __construct(VisualizationAssessment $model)
    {
        parent::__construct($model);
    }

    const NOT_PERMISSION_MESSAGE = "У вас недостаточно прав для выполнения этой операции";

    /**
     * @param array $data
     * @return Model|null
     * @throws CustomException
     */
    public function store(array $data): ?Model
    {
        try {

            /**
             * Admin & commission can create or edit assessment
             */
            $role = Auth::user()->roles->pluck('name')[0];
            if (!in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::COMMISSION()->value])) {
                throw new CustomException(
                    AssessmentService::NOT_PERMISSION_MESSAGE,
                    Response::HTTP_FORBIDDEN
                );
            }

            $data['comment'] = !array_key_exists('comment', $data) || is_null($data['comment']) ? "" : $data['comment'];
            $model = $this->model
                ->query()
                ->create($data);
            return $this->findById($model->id);
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id, int $userApplicationId): Model|Collection|Builder|array|null
    {
        try {

            /**
             * Admin & managers sees all the ratings, the curator sees only his own rating
             */
            $role = Auth::user()->roles->pluck('name')[0];
            $userId = in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])
                ? null : Auth::id();

            $where = $userId ? ['user_id' => $userId] : ['user_application_id' => $userApplicationId];


            return $this->model
                ->query()
                ->where($where)
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getAll(
        string $sortBy,
        string $orderBy,
        int    $userApplicationId,
        array  $withRelation = [],
        array  $allowedFilters = [],
        array  $allowedFields = [],
        array  $allowedIncludes = [],
        Model  $model = null,
        int    $perPage = null,
        int    $page = null,
        int    $userId = null,
    ): Collection|LengthAwarePaginator
    {
        $pageName = 'page';
        /**
         * Order by any column ASC & DESC
         */
        $order = strtolower($orderBy) == self::DESC ? '-' : '';
        if ($sortBy == 'name' || $sortBy == 'description') {
            $sortBy = $sortBy . '->' . app()->getLocale();
        }

        $query = QueryBuilder::for($model)
            ->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($order . $sortBy);

        if ($userId) {
            $query = $query->where([
                'user_id'             => $userId,
                'user_application_id' => $userApplicationId
            ]);
        } else {
            $query = $query->where([
                'user_application_id' => $userApplicationId
            ]);
        }

        return $perPage !== null & $query->count() > $perPage
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function findAssessmentById(int $id, int $userApplicationId): Model|Collection|Builder|array|null
    {
        try {

            /**
             * Admin & managers sees all the ratings, the curator sees only his own rating
             */
            $role = Auth::user()->roles->pluck('name')[0];
            $userId = in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])
                ? null : Auth::id();

            $where = $userId ? ['user_id' => $userId] : ['user_application_id' => $userApplicationId];


            return $this->model
                ->query()
                ->where($where)
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id, int $userApplicationId): bool
    {

        try {
            /**
             * Admin & commission can create or edit assessment
             */
            $role = Auth::user()->roles->pluck('name')[0];
            if (!in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::COMMISSION()->value])) {
                throw new CustomException(
                    self::NOT_PERMISSION_MESSAGE,
                    Response::HTTP_FORBIDDEN
                );
            }
            return $this->softDelete($this->findAssessmentById($id, $userApplicationId));
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
