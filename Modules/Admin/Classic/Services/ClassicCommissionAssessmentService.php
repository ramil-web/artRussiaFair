<?php

namespace Admin\Classic\Services;

use Admin\Classic\Repositories\ClassicCommissionAssessmentRepository;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClassicCommissionAssessmentService
{
    const NOT_PERMISSION_MESSAGE = "У вас недостаточно прав для выполнения этой операции";

    public function __construct(public ClassicCommissionAssessmentRepository $repository)
    {
    }

    /**
     * @param array $data
     * @return Model|null
     * @throws CustomException
     */
    public function create(array $data): ?Model
    {
        $data['user_id'] = Auth::id();
        $comment = $this->repository->create($data);
        return $this->repository->findById($comment->id);
    }

    /**
     * @param int $id
     * @param int $assessmentId
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id, int $assessmentId): Model|Collection|Builder|array|null
    {
        return $this->repository->findAssessmentById($id, $assessmentId);
    }

    public function list(int $id, Request $request)
    {
        $withRelation = ['user'];
        $allowedFields = [
            'id',
            'comment',
            'created_at',
            'updated_at'
        ];
        $allowedIncludes = [
            'user',
        ];

        $allowedSorts = ['id', 'comment', 'created_at', 'updated_at'];

        $perPage = $request->has('per_page') ? $request->per_page : 20;

        /**
         * Admin & managers sees all the ratings, the curator sees only his own rating
         */
        $role = Auth::user()->roles->pluck('name')[0];
        $userId = in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])
            ? null : Auth::id();

        return $this->repository->getAll(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $perPage,
            $userId
        );
    }

    /**
     * @param int $id
     * @param int $assessmentId
     * @param mixed $data
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function update(int $id, int $assessmentId, mixed $data): Model|Collection|Builder|array|null
    {
        $model = $this->repository->findAssessmentById($id, $assessmentId);
        $this->repository->update($model, $data);
        return $this->repository->findAssessmentById($id, $assessmentId);
    }

    /**
     * @param int $id
     * @param $assessmentId
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id, $assessmentId): bool
    {
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
        return $this->repository->softDelete(
            $this->repository->findAssessmentById($id, $assessmentId)
        );
    }
}
