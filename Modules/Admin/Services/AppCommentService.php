<?php

namespace Admin\Services;

use Admin\Events\AdminNewCommentEvent;
use Admin\Repositories\UserApplication\UserApplicationCommentRepository;
use App\Exceptions\CustomException;
use App\Models\User;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AppCommentService
{
    private UserApplicationCommentRepository $commentRepository;

    public function __construct(UserApplicationCommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function create(array $data): Model
    {
        $data['user_id'] = Auth::id();
        $userModel = User::query()->find($data['user_id']);
        $comment = $this->commentRepository->create($data);
        broadcast(new AdminNewCommentEvent($userModel, $comment))->toOthers();
        return $this->commentRepository->findById($comment->id);
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
        return $this->commentRepository->getAll(
            $id,
            $withRelation,
            $allowedFields,
            $allowedIncludes,
            $allowedSorts,
            false,
            $perPage
        );
    }

    /**
     * @param int $comment_id
     * @return Model
     */
    public function show(int $comment_id): Model
    {
        return $this->commentRepository->findById($comment_id);
    }

    /**
     * @param int $comment_id
     * @param array $data
     * @return Model
     */
    public function update(int $comment_id, array $data): Model
    {
        $user = Auth::user();
        $this->commentRepository->update($this->commentRepository->findById($comment_id), $data);
        $comment =  $this->commentRepository->findById($comment_id);
        broadcast(new AdminNewCommentEvent($user, $comment))->toOthers();
        return $comment;
    }

    /**
     * @param int $id
     * @param int $commentId
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id, int $commentId): bool
    {
        try {
            $user = Auth::user();
            $model = $this->commentRepository->findComment($id, $commentId);
            $deleted = $model->toArray();
            broadcast(new AdminNewCommentEvent($user, $deleted))->toOthers();
            return $this->commentRepository->softDelete($model);
        } catch (Throwable $e) {
            throw new CustomException($e, Response::HTTP_BAD_REQUEST);
        }
    }

}
