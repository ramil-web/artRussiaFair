<?php

namespace Admin\Services;

use Admin\Repositories\UserApplication\UserApplicationAssessmentRepository;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssessmentService
{
    private UserApplicationAssessmentRepository $assessmentRepository;

    const NOT_PERMISSION_MESSAGE = "У вас недостаточно прав для выполнения этой операции";

    public function __construct(UserApplicationAssessmentRepository $assessmentRepository)
    {
        $this->assessmentRepository = $assessmentRepository;
    }


    /**
     * @throws CustomException
     */
    public function create(array $data): Model
    {
        $data['user_id'] = \Auth::id();
        $comment = $this->assessmentRepository->create($data);

        return $this->assessmentRepository->findById($comment->id);
    }

    public function list(int $id, Request $request)
    {
        $withRelation = ['user'];
        $allowedFields = [
            'id',
            'message',
            'created_at',
            'updated_at'
        ];
        $allowedIncludes = [
            'user',
        ];

        $allowedSorts = ['id', 'message', 'created_at', 'updated_at'];

        $perPage = $request->has('per_page') ? $request->per_page : 20;

        /**
         * Admin & managers sees all the ratings, the curator sees only his own rating
         */
        $role = Auth::user()->roles->pluck('name')[0];
        $userId = in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])
            ?  null: Auth::id();

        return $this->assessmentRepository->getAll(
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
     * @return Model
     * @throws CustomException
     */
    public function show(int $id, int $assessmentId): Model
    {
        return $this->assessmentRepository->findAssessmentById($id, $assessmentId);
    }

    /**
     * @param int $id
     * @param int $assessmentId
     * @param array $data
     * @return Model
     * @throws CustomException
     */
    public function update(int $id, int $assessmentId, array $data): Model
    {
        $model = $this->assessmentRepository->findAssessmentById($id, $assessmentId);
        $this->assessmentRepository->update($model, $data);
        return $this->assessmentRepository->findAssessmentById($id, $assessmentId);
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
        return $this->assessmentRepository->softDelete(
            $this->assessmentRepository->findAssessmentById($id, $assessmentId)
        );
    }
}
