<?php


namespace Admin\Repositories\User;


use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;


class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getUserByFilters(
        array $role = [],
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        bool  $withTrashed = false,
        int   $perPage = null,
              $page = null
    )
    {
        $query = QueryBuilder::for($this->model);
        count($role) !== 0 ? $query = $query->role($role) : $query;
        $query = $query->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort('id')
            ->allowedSorts($allowedSorts);

        $withTrashed ? $query = $query->withTrashed() : $query;
        Paginator::currentPageResolver(fn() => $page);
        return $query->jsonPaginate($perPage);
    }

    /**
     * @param int $id
     * @return bool|null
     * @throws CustomException
     */
    public function deleteParticipant(int $id): bool|null
    {
        try {
            $user = $this->model
                ->query()
                ->with('roles')
                ->find($id);
            foreach ($user->roles as $role) {
                if ($role->name != 'participant') {
                    throw new CustomException("Пользователь не является участником, вы не можете удалить его!", Response::HTTP_FORBIDDEN);
                }
            }
            return $user->forceDelete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function forceDeleteUser(int $id): bool
    {
        try {
            return $this->model->query()->findOrFail($id)->forceDelete();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
