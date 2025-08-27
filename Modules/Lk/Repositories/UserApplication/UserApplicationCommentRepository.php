<?php

namespace Lk\Repositories\UserApplication;

use App\Models\AppComment;
use Arr;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\BaseRepository;
use Spatie\QueryBuilder\QueryBuilder;

class UserApplicationCommentRepository extends BaseRepository
{

    public function __construct(AppComment $model)
    {
        $this->model = $model;
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
}
