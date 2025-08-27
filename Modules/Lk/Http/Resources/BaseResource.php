<?php

namespace Lk\Http\Resources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Lk\Http\Resources\Relation\DataResource;
use Lk\Http\Resources\Relation\LinkResource;

class BaseResource extends JsonResource
{
    protected array $includes;

    protected array $attributes;

    protected string $type;

    protected string $namespace;

    protected array $relationships;

    public function __construct($resource)
    {
        parent::__construct($resource);
//        dd($this->type);
        $this->includes = $this->getLoadedRelations();
        $this->attributes = $this->attributes ?? $resource->toArray();
        $this->type = $this->type ?? get_class($resource)::MODEL_TYPE;
    }

    public function toArray($request): array
    {
        $data = [
            'id' => (string)$this->resource->id,
            'type' => $this->type,
            'attributes' => $this->attributes,
            'links' => $this->withLinks(),
//            'relationships' => $this->relationships

        ];
        if (!empty($this->relationships)) {
            $data = array_merge($data, ['relationships' => $this->withRelationships()]);
        }
        return $data;
    }

    public function with($request): array
    {
        $with = [
            'links' => $this->withLinks()
        ];

        if (!empty($this->includes)) {
            $with = array_merge($with, ['include' => $this->withInclude()]);
        }

        return $with;
    }

    public function withResponse($request, $response)
    {
        if ($request->getMethod() === 'POST') {
            $response->header('Location', route("{$this->namespace}{$this->type}.show", ['id' => $this->resource->id]));
        }
    }

    private function withInclude(): array
    {
        $include = [];
//dd($this->includes);
        foreach ($this->includes as $includes) {
            $relations = explode('.', $includes);
            $relation = reset($relations);
            if ($this->resource->{$relation} instanceof Collection) {
                foreach ($this->resource->{$relation} as $model) {
//                    dd($model);
                    $include[] = $this->getResource($relation, $model);
                    if (count($relations) > 1) {
                        foreach ($model->{$relations[1]} as $model1) {
                            $include[] = $this->getResource($relations[1], $model1);
                        }
                    }
                }
            } elseif ($this->resource->{$relation} !== null) {
                    $include[] = $this->getResource($relation, $this->resource->{$relation});
                }

        }

        return $include;
    }

    private function withRelationships(): array
    {
        $relationships = [];
//dd($this->relationships);
        foreach ($this->relationships as $relation) {
            $relationships[$relation] = array_merge(
                $this->getRelationshipsData($relation),
                $this->getRelationshipsLinks($relation)
            );
        }

        return $relationships;
    }

    private function getRelationshipsData($relation): array
    {
//        dd($relation);
//        $relation = $this->whenLoaded($relation);
        $relation = $this->resource->{$relation};
        if ($relation instanceof Collection) {
            return ['data' => DataResource::collection($relation)];
        } else {
            return ['data' => new DataResource($relation)];
        }
    }

    private function getRelationshipsLinks($relation): array
    {

//        dd($this->type);
        return [
            'links' => new LinkResource([
                'relation' => $relation,
                'id' => $this->resource->id,
                'entity' => $this->type,
                'namespace'=>$this->namespace,
            ])
        ];
    }

    private function getLoadedRelations(): array
    {
        $include = request()->query('include');

        if ($include) {
            $include = explode(',', $include);
        } else {
            if (!empty($this->relationships)) {
                $include = [];
                foreach ($this->relationships as $relation) {
                    if ($this->whenLoaded($relation) instanceof Collection || $this->whenLoaded(
                            $relation
                        ) instanceof Model) {
                        $include[] = $relation;
                    }
                }
            }
        }

        return (array)$include;
    }

    private function withLinks(): array
    {
//        dd($this->type);
        return [
            'self' => route("{$this->namespace}{$this->type}.show", ['id' => $this->resource->id])
        ];
    }

    private function getResource($relation, $model): JsonResource
    {
        $resource = 'Lk\\Http\\Resources\\' . Str::studly(Str::singular($relation)) . '\\' . Str::studly(
                Str::singular($relation)
            ) . 'Resource';

        return new $resource($model);
    }
}
