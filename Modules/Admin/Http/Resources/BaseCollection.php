<?php

namespace Admin\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class BaseCollection extends ResourceCollection
{
    protected array $includes;

    protected string $type;

    protected string $namespace;

    public function __construct($collection)
    {
        parent::__construct($collection);

        $this->includes = $this->setIncludes();
    }

    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function with($request)
    {
        $include = [];

        $links = ['links' => $this->withLinks()];

        if ($this->includes && !$this->collection->isEmpty()) {
            $include = ['include' => $this->getInclude()];
        }

        return array_merge($links, $include);
    }

    private function getInclude(): array
    {
        $resources = [];
        foreach ($this->collection as $resource) {
            foreach ($this->includes as $includes) {
                $relations = explode('.', $includes);
                $relation = reset($relations);
                if ($resource->{$relation} instanceof Collection) {
                    foreach ($resource->{$relation} as $model) {
                        $resources[$relation][$model->id] = $this->getResource($relation, $model);
                        if (count($relations) > 1) {
                            foreach ($model->{$relations[1]} as $model1) {
                                $resources[$relations[1]][$model1->id] = $this->getResource($relations[1], $model1);
                            }
                        }
                    }
                } elseif ($resource->{$relation} !== null) {
                    $resources[$relation][$resource->{$relation}->id] = $this->getResource(
                        $relation,
                        $resource->{$relation}
                    );
                }
            }
        }

        $relations = [];
        foreach ($this->includes as $includes) {
            $includes = explode('.', $includes);
            foreach ($includes as $include) {
                if (isset($resources[$include])) {
                    $relations = array_merge($relations, $resources[$include]);
                }
            }
        }

        return $relations;
    }

    private function setIncludes(): array
    {
        $include = request()->query('include');

        if ($include) {
            $include = explode(',', $include);
        } else {
            $include = [];
            if (!empty($this->relationships)) {
                foreach ($this->relationships as $relation) {
                    if ($this->whenLoaded($relation) instanceof \Illuminate\Support\Collection || $this->whenLoaded(
                            $relation
                        ) instanceof Model) {
                        $include[] = $relation;
                    }
                }
            }
        }

        return $include;
    }

    private function withLinks(): array
    {
        return [
            'self' => route("{$this->namespace}$this->type.index")
        ];
    }

    private function getResource($relation, $model): JsonResource
    {
        $resource = 'Admin\\Http\\Resources\\' . Str::studly(Str::singular($relation)) . '\\' . Str::studly(
                Str::singular($relation)
            ) . 'Resource';

        return new $resource($model);
    }
}
