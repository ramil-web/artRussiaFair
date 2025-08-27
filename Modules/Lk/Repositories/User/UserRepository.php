<?php

namespace Lk\Repositories\User;

use App\Exceptions\CustomException;
use App\Models\User;
use Auth;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;

class UserRepository extends BaseRepository
{

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function deleteUser(int $id): bool
    {
        try {
            $currentUser = Auth::user();
            if ($currentUser->id != $id) {
                throw  new CustomException(
                    "Недостаточно прав, пользовател может удалить толькло свой профиль",
                    Response::HTTP_FORBIDDEN
                );
            }
            $user = $this->model->query()->findOrFail($id);
            return $this->forceDelete($user);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), $e->getCode());
        }
    }
}
