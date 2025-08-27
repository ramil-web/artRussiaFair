<?php

namespace Lk\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Lk\Http\Resources\Relation\RelationshipCollection;
use Lk\Http\Resources\Relation\RelationshipResource;
use Lk\Services\Relation\RelationService;

class RelationController extends Controller
{
    private RelationService $relationService;

    public function __construct(RelationService $relationService)
    {
        $this->relationService = $relationService;
    }

    public function relationships($entity, $id, $relation): RelationshipCollection|RelationshipResource
    {
        $query = $this->relationService->relations($entity, $id, $relation);

        if ($query instanceof Collection) {
            return new RelationshipCollection($query);
        }

        return new RelationshipResource($query);
    }

    public function relations($entity, $id, $relation)
    {
        if ($entity === 'manager') {
            $entity = 'users';
            $relation = 'managerProfile';
        } elseif ($entity === 'users') {
            $relation = 'userProfile';
        }


        $query = $this->relationService->relations($entity, $id, $relation);

        $modelName = $this->relationService->getModelName($relation);

        $resource = 'Lk\\Http\\Resources\\' . $modelName . '\\' . $modelName;

        if ($query instanceof Collection) {
            $resource .= 'Collection';
        } else {
            $resource .= 'Resource';
        }

        return new $resource($query);
    }
}
