<?php

namespace Admin\Repositories\UserApplication;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\AppComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class UserApplicationCommentRepository extends BaseRepository
{
    public function __construct(AppComment $model)
    {
        parent::__construct($model);
    }

    public function getAll(
        int   $id,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        int   $perPage = null
    )
    {
        $query = QueryBuilder::for($this->model)
            ->where('user_application_id', $id)
            ->with($withRelation)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->defaultSort('created_at')
            ->allowedSorts($allowedSorts);

        return $perPage !== null
            ? $query->jsonPaginate($perPage)
            : $query->get();
    }

    public function create(array $Data): Model
    {

        $locale = $Data['locate'] ?? app()->getLocale();

        $translate = config('transletable.comments');
        foreach ($translate as $value) {
            $Data[$value] = [$locale => $Data[$value]];
        }
        return $this->model->Create($Data);
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

    public function findById(
        int   $modelId,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        bool  $withTrashed = false
    ): ?Model
    {
        return QueryBuilder::for($this->model)
            ->with($withRelation)
            ->allowedFields($allowedFields)
            ->allowedIncludes($allowedIncludes)
            ->findOrFail($modelId);
    }

    /**
     * @param int $id
     * @param int $commentId
     * @return Model|Collection|Builder|array|null
     * @throws CustomException
     */
    public function findComment(int $id, int $commentId): Model|Collection|Builder|array|null
    {
        try {
            return $this->model
                ->query()
                ->where('user_application_id', $id)
                ->findOrFail($commentId);
        } catch (QueryException $e) {
            throw new CustomException($e, Response::HTTP_BAD_REQUEST);
        }
    }
}
