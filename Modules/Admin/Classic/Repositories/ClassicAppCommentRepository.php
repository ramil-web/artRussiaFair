<?php

namespace Admin\Classic\Repositories;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\ClassicAppComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ClassicAppCommentRepository extends BaseRepository
{
    public function __construct(ClassicAppComment $model)
    {
        parent::__construct($model);
    }

    public function getAll(
        int   $id,
        array $withRelation,
        array $allowedFields,
        array $allowedIncludes,
        array $allowedSorts,
        false $false,
        mixed $perPage)
    {
        $query = QueryBuilder::for($this->model);
        $query = $query->where('classic_user_application_id', $id);
        $query = $query->with($withRelation);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        $query = $query->defaultSort('created_at')
            ->allowedSorts($allowedSorts);

        return $perPage !== null & $query->count() > $perPage ? $query->jsonPaginate($perPage) : $query->get();
    }

    public function create(array $Data): Model
    {
        $locale = $Data['locate'] ?? app()->getLocale();
        $translate = config('transletable.comments');
        foreach ($translate as $value) {
            $Data[$value] = [$locale => $Data[$value]];
        }
        return $this->model->query()->create($Data);
    }

    public function findById(
        int   $modelId,
        array $withRelation = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        bool  $withTrashed = false
    ): ?Model
    {
        $query = QueryBuilder::for($this->model);
        $query = $query->with($withRelation);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        return $query->findOrFail($modelId);
    }

    /**
     * @param int $commentId
     * @param int $id
     * @return Model|Builder|null
     * @throws CustomException
     */
    public function getComment(int $commentId, int $id): Model|null|Builder
    {
        try {
            return $this->model
                ->query()
                ->where([
                    'id'                          => $commentId,
                    'classic_user_application_id' => $id
                ])
                ->firstOrFail();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
