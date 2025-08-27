<?php

namespace Lk\Services;

use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Lk\Repositories\UserApplication\UserApplicationCommentRepository;
use Symfony\Component\HttpFoundation\Response;

class AppCommentService
{

    private UserApplicationCommentRepository $commentRepository;


    public function __construct(UserApplicationCommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
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

        $withTrashed = false;

        $request->has('per_page') ? $perPage = $request->per_page : $perPage = null;
        $comments = $this->commentRepository->getAll($id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            $withTrashed,
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

    public function create(array $data): Model
    {
        $data['user_id'] = \Auth::id();
        $comment = $this->commentRepository->create($data);

        return $this->commentRepository->findById($comment->id);
    }

    /**
     * @throws CustomException
     */
    public function show(int $comment_id, Request $request): Model
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

        $comment = $this->commentRepository->findById(
            $comment_id,
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

    public function update(int $comment_id, array $data): Model
    {
//        dd($comment_id,$this->commentRepository->findById($comment_id));

        $this->commentRepository->update($this->commentRepository->findById($comment_id), $data);

        return $this->commentRepository->findById($comment_id);
    }

    public function delete(int $id)
    {
        return $this->commentRepository->softDelete($this->commentRepository->findById($id));
    }
}
