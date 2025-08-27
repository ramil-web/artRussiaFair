<?php

namespace Admin\Repositories\VisualizationComment;

use Admin\Events\AdminNewCommentEvent;
use Admin\Repositories\BaseRepository;
use App\Enums\UserRoleEnum;
use App\Exceptions\CustomException;
use App\Models\VisualizationComment;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class VisualizationCommentRepository extends BaseRepository
{
    public function __construct(VisualizationComment $model)
    {
        parent::__construct($model);
    }

    /**
     * @throws CustomException
     */
    public function create(array $Data): Model
    {

        try {
            $locale = $Data['locate'] ?? app()->getLocale();
            $translate = config('transletable.comments');
            foreach ($translate as $value) {
                $Data[$value] = [$locale => $Data[$value]];
            }
            return $this->model->query()->create($Data);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return Model|Builder
     * @throws CustomException
     */
    public function show(int $id, int $userApplicationId): Model|Builder
    {
        try {

            /**
             * Admin & managers sees all the ratings, the curator sees only his own rating
             */
            $role = Auth::user()->roles->pluck('name')[0];
            $userId = in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])
                ? null : Auth::id();

            $where = $userId ? ['user_id' => $userId] : ['user_application_id' => $userApplicationId];

            return $this->model
                ->query()
                ->where($where)
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $sortBy
     * @param string $orderBy
     * @param int $userApplicationId
     * @param array $withRelation
     * @param array $allowedFilters
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param Model|null $model
     * @param int|null $perPage
     * @param int|null $page
     * @param int|null $userId
     * @return Collection|LengthAwarePaginator
     */
    public function getAll(
        string $sortBy,
        string $orderBy,
        int    $userApplicationId,
        array  $withRelation = [],
        array  $allowedFilters = [],
        array  $allowedFields = [],
        array  $allowedIncludes = [],
        Model  $model = null,
        int    $perPage = null,
        int    $page = null,
        int    $userId = null
    ): Collection|LengthAwarePaginator
    {
        $pageName = 'page';
        /**
         * Order by any column ASC & DESC
         */
        $order = strtolower($orderBy) == self::DESC ? '-' : '';
        if ($sortBy == 'name' || $sortBy == 'description') {
            $sortBy = $sortBy . '->' . app()->getLocale();
        }

        $query = QueryBuilder::for($model)
            ->with($withRelation)
            ->allowedFilters($allowedFilters)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($order . $sortBy);

        if ($userId) {
            $query = $query->where([
                'user_id'             => $userId,
                'user_application_id' => $userApplicationId
            ]);
        } else {
            $query = $query->where([
                'user_application_id' => $userApplicationId
            ]);
        }

        return $perPage !== null & $query->count() > $perPage
            ? $query->paginate($perPage, $allowedFields, $pageName, $page)
            : $query->get();
    }

    public function update(Model $model, array $Data): bool
    {
        $locale = $Data['locate'] ?? app()->getLocale();
        $translate = config('transletable.comments');
        foreach ($translate as $value) {
            $model->setTranslations($value, [$locale => $Data[$value]]);
            Arr::except($Data, $value);
        }
        return $model->update($Data);
    }

    /**
     * @param int $id
     * @param $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function deleteComment(int $id, $userApplicationId): bool
    {
        /**
         * Admin & managers sees all the ratings, the curator sees only his own rating
         */
        $user = Auth::user();
        $model = $this->findCommentById($id, $userApplicationId);
        $deleted = $model->toArray();
        broadcast(new AdminNewCommentEvent($user, $deleted))->toOthers();
        return $this->softDelete($model);
    }

    /**
     * @param int $id
     * @param int $userApplicationId
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function findCommentById(int $id, int $userApplicationId): Model|Collection|Builder|array|null
    {
        try {

            /**
             * Admin & managers sees all the ratings, the curator sees only his own rating
             */
            $role = Auth::user()->roles->pluck('name')[0];
            $userId = in_array($role, [UserRoleEnum::SUPER_ADMIN()->value, UserRoleEnum::MANAGER()->value])
                ? null : Auth::id();

            $where = $userId ? ['user_id' => $userId] : ['user_application_id' => $userApplicationId];


            return $this->model
                ->query()
                ->where($where)
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
