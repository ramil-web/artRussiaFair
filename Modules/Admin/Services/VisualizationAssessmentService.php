<?php

namespace Admin\Services;

use Admin\Repositories\VisualizationAssessment\VisualizationAssessmentRepository;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\VisualizationAssessment;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;

class VisualizationAssessmentService
{
    public function __construct(
        public VisualizationAssessmentRepository $repository,
        public VisualizationAssessment           $visualizationAssessment
    ) {
    }

    /**
     * @param array $data
     * @return Model|null
     * @throws CustomException
     */
    public function store(array $data): ?Model
    {
        $data['user_id'] = Auth::id();
        return $this->repository->store($data);
    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function show(int $id, int $userApplicationId): Model|Collection|Builder|array|null
    {
        return $this->repository->show($id, $userApplicationId);
    }

    /**
     * @param array $appData
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function update(array $appData): Model|Collection|Builder|array|null
    {
        $model = $this->repository->findAssessmentById($appData['id'], $appData['user_application_id']);
        $this->repository->update($model, $appData);
        return $this->repository->findAssessmentById($appData['id'], $appData['user_application_id']);
    }

    /**
     * @param array $appData
     * @return Collection|LengthAwarePaginator
     */
    public function list(array $appData): Collection|LengthAwarePaginator
    {
        $withRelation = ['user'];
        $allowedFields = [
            'id',
            'user_application_id',
            'message',
            'created_at',
            'updated_at'
        ];
        $allowedIncludes = [];

        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('user_application_id'),
        ];


        $sortBy = array_key_exists('sort_by', $appData) ? $appData['sort_by'] : 'id';
        $orderBy = array_key_exists('order_by', $appData) ? $appData['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $appData) ? $appData['per_page'] : null;
        $page = array_key_exists('page', $appData) ? $appData['page'] : null;

        /**
         * Admin & managers sees all the ratings, the curator sees only his own rating
         */
        $role = Auth::user()->roles->pluck('name')[0];
        $userId = in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])
            ? null : Auth::id();

        return $this->repository->getAll(
            $sortBy,
            $orderBy,
            $appData['user_application_id'],
            $withRelation,
            $allowedFilters,
            $allowedFields,
            $allowedIncludes,
            $this->visualizationAssessment,
            $perPage,
            $page,
            $userId,
        );
    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id, int $userApplicationId): bool
    {
        return $this->repository->delete($id, $userApplicationId);
    }
}
