<?php

namespace Admin\Repositories\UserApplication;

use Admin\Repositories\BaseRepository;
use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use App\Models\User;
use App\Models\UserApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class UserApplicationRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(UserApplication $model)
    {
        parent::__construct($model);
    }

    public function update(Model $model, array $Data): bool
    {
        if (array_key_exists('status', $Data)) {
            if ($Data['status'] == AppStatusEnum::REJECTED) {
                $Data['active'] = false;
            }
        }
        return $model->update($Data);
    }

    public function findUserAppById(
        int   $modelId,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
    ): ?Model
    {
        $query = QueryBuilder::for($this->model);
        $query = $query->with($withRelation);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        return $query->findOrFail($modelId);
    }

    /**
     * @param int $id
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function findUser(int $id): Model|Collection|Builder|array|null
    {
        try {
            return User::query()
                ->findOrFail($id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function visitor(array $dataApp): Model|Collection|Builder|array|null
    {
        try {
            $userApp = $this->model
                ->query()
                ->findOrFail($dataApp['user_application_id']);
            $data = $this->checkVisitor($dataApp, $userApp->visitor);
            $this->update($userApp, ['visitor' => $data]);
            return $this->model->query()->findOrFail($dataApp['user_application_id']);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param array $dataApp
     * @param array $visitor
     * @return array
     * @throws CustomException
     */
    private function checkVisitor(array $dataApp, array $visitor): array
    {
        try {
            $userIds = array_column($visitor, 'user_id');
            $data = [
                'user_id' => $dataApp['user_id'],
                'email'   => $dataApp['email'],
                'role'    => $dataApp['role'],
            ];
            if (!in_array($dataApp['user_id'], $userIds)) {
                $visitor[] = $data;
            }
            return $visitor;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param string $sort
     * @param array $withRelation
     * @param array $allowedFields
     * @param array $allowedFilters
     * @param array $allowedIncludes
     * @param Model|null $model
     * @param int|null $perPage
     * @param int|null $page
     * @return Collection|LengthAwarePaginator
     */
    public function getUserApplications(
        string $sort,
        array  $withRelation = [],
        array  $allowedFields = [],
        array  $allowedFilters = [],
        array  $allowedIncludes = [],
        Model  $model = null,
        int    $perPage = null,
        int    $page = null,
    ): Collection|LengthAwarePaginator
    {
        $withCount = "visualization";
        $defaultSort = '-created_at';
        $pageName = 'page';

        if (in_array($sort, ['organization', '-organization', 'full_name', '-full_name'])) {
            $sort = $sort . '->' . app()->getLocale();
        }

        $query = QueryBuilder::for($model);
        $query = $query->with($withRelation)
            ->select($allowedFields)
            ->allowedFilters($allowedFilters)
            ->withCount($withCount)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort($defaultSort)
            ->allowedSorts([$sort]);
        return $query->paginate($perPage, $allowedFields, $pageName, $page);
    }
}
