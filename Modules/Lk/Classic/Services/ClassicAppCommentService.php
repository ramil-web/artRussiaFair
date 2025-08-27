<?php

namespace Lk\Classic\Services;

use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use Illuminate\Http\Request;
use Lk\Classic\Repositories\ClassicAppCommentRepository;
use Symfony\Component\HttpFoundation\Response;

class ClassicAppCommentService
{
    public function __construct(public ClassicAppCommentRepository $repository)
    {
    }

    /**
     * @throws CustomException
     */
    public function show(int $commentId, Request $request)
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

        $request->has('with_trashed') ? $withTrashed = true : $withTrashed = false;

        $comment = $this->repository->findById(
            $commentId,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $withTrashed
        );

        $role = $comment->user->roles()->pluck('name')[0];
        if (!in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])) {
            throw  new CustomException("Ресурс не найден", Response::HTTP_NOT_FOUND);
        }
        return $comment;
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

        $request->has('per_page') ? $perPage = $request->per_page : $perPage = null;
        $comments = $this->repository->getAll($id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            false,
            $perPage
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
