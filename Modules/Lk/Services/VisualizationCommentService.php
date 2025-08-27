<?php

namespace Lk\Services;

use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\VisualizationComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\VisualizationComment\VisualizationCommentRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Symfony\Component\HttpFoundation\Response;

class VisualizationCommentService
{
    public function __construct(
        public VisualizationCommentRepository $repository,
        public VisualizationComment           $visualizationComment
    )
    {
    }

    /**
     * @param int $id
     * @return Model|null
     * @throws CustomException
     */
    public function show(int $id): ?Model
    {
        $withRelation = ['user'];

        $allowedFields = [
            'id',
            'visualization_id',
            'user_application_id',
            'message',
            'created_at',
            'updated_at'
        ];
        $allowedIncludes = [
            'user',
        ];


        $comment = $this->repository->findById(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
        );

        $role = $comment->user->roles()->pluck('name')[0];
        if (!in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])) {
            throw  new CustomException("Ресурс не найден", Response::HTTP_NOT_FOUND);
        }
        return $comment;
    }

    /**
     * @param mixed $appData
     * @return Collection|LengthAwarePaginator|array
     */
    public function list(mixed $appData): Collection|LengthAwarePaginator|array
    {
        $withRelation = ['user'];

        $allowedFields = [
            'id',
            'visualization_id',
            'user_application_id',
            'message',
            'created_at',
            'updated_at'
        ];
        $allowedIncludes = [
            'user',
        ];

        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('visualization_id'),
            AllowedFilter::exact('user_application_id'),
            AllowedFilter::trashed(),
        ];

        $sortBy = array_key_exists('sort_by', $appData) ? $appData['sort_by'] : 'id';
        $orderBy = array_key_exists('order_by', $appData) ? $appData['order_by'] : 'ASC';
        $perPage = array_key_exists('per_page', $appData) ? $appData['per_page'] : null;
        $page = array_key_exists('page', $appData) ? $appData['page'] : null;
        $comments = $this->repository->getAllWithPaginate(
            $appData['user_application_id'],
            $this->visualizationComment,
            $allowedFilters,
            $orderBy,
            $page,
            $perPage,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $sortBy
        );

        foreach ($comments as $key => $comment) {
            $role = $comment->user->roles()->pluck('name')[0];
            if (!in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])) {
                unset($comments[$key]);
            }
        }
        return $comments;
    }
}
