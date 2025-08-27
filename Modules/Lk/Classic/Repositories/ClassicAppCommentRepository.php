<?php

namespace Lk\Classic\Repositories;

use App\Models\ClassicAppComment;
use Lk\Repositories\BaseRepository;
use Spatie\QueryBuilder\QueryBuilder;

class ClassicAppCommentRepository extends BaseRepository
{
public function __construct(ClassicAppComment $model)
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
        $query = QueryBuilder::for($this->model);
        $query = $query->where('classic_user_application_id', $id);
        $query = $query->with($withRelation);
        $query = $query->allowedFields($allowedFields);
        $query = $query->allowedIncludes($allowedIncludes);
        $query = $query->defaultSort('created_at')
            ->allowedSorts($allowedSorts);

        return $perPage !== null & $query->count() > $perPage ? $query->jsonPaginate($perPage) : $query->get();
    }
}
