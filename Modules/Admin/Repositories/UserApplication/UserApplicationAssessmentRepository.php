<?php

namespace Admin\Repositories\UserApplication;

use Admin\Repositories\BaseRepository;
use Admin\Services\AssessmentService;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\CommissionAssessment;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class UserApplicationAssessmentRepository extends BaseRepository
{


    public function __construct(CommissionAssessment $model)
    {
        parent::__construct($model);
    }

    public function getAll(
        int   $id,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        int   $perPage = null,
        int   $userId = null
    )
    {
        $query = QueryBuilder::for($this->model);
        $query = $query->where('user_application_id', $id);
        $query = $query->with($withRelation);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        $query = $query->defaultSort('created_at')
            ->allowedSorts($allowedSorts);

        if ($userId) {
            $query = $query->where('user_id', $userId);
        }

        return $perPage !== null & $query->count() > $perPage ? $query->jsonPaginate($perPage) : $query->get();
    }

    /**
     * @throws CustomException
     */
    public function create(array $Data): Model
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

            return $this->model
                ->query()
                ->create($Data);
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws CustomException
     */
    public function update(Model $model, array $Data): bool
    {
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
        return $model->update($Data);
    }

    /**
     * @param int $id
     * @param int $assessmentId
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function findAssessmentById(int $id, int $assessmentId): Model|Collection|Builder|array|null
    {
        try {

            /**
             * Admin & managers sees all the ratings, the curator sees only his own rating
             */
            $role = Auth::user()->roles->pluck('name')[0];
            $userId = in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])
                ? null : Auth::id();

            $where = $userId ? ['user_id' => $userId] : ['user_application_id' => $id];


            return $this->model
                ->query()
                ->where($where)
                ->findOrFail($assessmentId);
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
