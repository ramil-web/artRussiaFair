<?php

namespace Admin\Classic\Services;

use Admin\Classic\Repositories\ClassicAppCommentRepository;
use Admin\Events\AdminNewCommentEvent;
use App\Exceptions\CustomException;
use App\Models\User;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ClassicAppCommentService
{
    public function __construct(public ClassicAppCommentRepository $repository)
    {
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
        return $this->repository->getAll(
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
     * @param array $data
     * @return Model|null
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
     * @param int $commentId
     * @param int $id
     * @return Model|null
     * @throws CustomException
     */
    public function show(int $commentId, int $id): ?Model
    {
        return $this->repository->getComment($commentId, $id);
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
            $model = $this->repository->getComment($commentId, $id);
            $deleted = $model->toArray();
            broadcast(new AdminNewCommentEvent($user, $deleted))->toOthers();
            return $this->repository->softDelete($model);
        } catch (Throwable $e) {
            throw new CustomException($e, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $comment_id
     * @param array $data
     * @return Model|null
     */
    public function update(int $comment_id, array $data): ?Model
    {
        $user = Auth::user();
        $this->repository->update($this->repository->findById($comment_id), $data);
        $comment = $this->repository->findById($comment_id);
        broadcast(new AdminNewCommentEvent($user, $comment))->toOthers();
        return $comment;
    }
}
