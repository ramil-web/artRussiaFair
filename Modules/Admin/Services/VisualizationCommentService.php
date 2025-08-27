<?php

namespace Admin\Services;

use Admin\Events\AdminNewCommentEvent;
use Admin\Repositories\VisualizationComment\VisualizationCommentRepository;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Models\VisualizationComment;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;

class VisualizationCommentService
{
    public function __construct(
        public VisualizationCommentRepository $repository,
        public VisualizationComment           $visualizationComment
    )
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
        $userModel = User::query()->find($data['user_id']);
        $comment = $this->repository->create($data);
        broadcast(new AdminNewCommentEvent($userModel, $comment))->toOthers();
        return $this->repository->findById($comment->id);
    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return Model|Builder
     * @throws CustomException
     */
    public function show(int $id, int $userApplicationId): Model|Builder
    {
        return $this->repository->show($id, $userApplicationId);
    }

    /**
     * @param mixed $appData
     * @return Collection|LengthAwarePaginator
     */
    public function list(mixed $appData): Collection|LengthAwarePaginator
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
            $this->visualizationComment,
            $perPage,
            $page,
            $userId,
        );
    }

    /**
     * @param array $appData
     * @return Model|null
     * @throws CustomException
     */
    public function update(array $appData): ?Model
    {
        $comment = $this->repository->findCommentById($appData['id'], $appData['user_application_id']);
        $this->repository->update($comment, $appData);
        $model = $this->repository->findCommentById($appData['id'], $appData['user_application_id']);
        $user = Auth::user();
        broadcast(new AdminNewCommentEvent($user, $model))->toOthers();
        return $model;
    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id, int $userApplicationId): bool
    {
        return $this->repository->deleteComment($id, $userApplicationId);
    }

}
