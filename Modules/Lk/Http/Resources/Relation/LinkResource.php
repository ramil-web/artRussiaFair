<?php

namespace Lk\Http\Resources\Relation;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use JsonSerializable;

class LinkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        $namespace = $this->resource['namespace'];
        $resource = Arr::except($this->resource, ['namespace']);
//       dd($resource);
        return [
            'self' => route($namespace . 'relationships.list', $resource),
            'related' => route($namespace . 'relations.list', $resource),
        ];
    }
}
