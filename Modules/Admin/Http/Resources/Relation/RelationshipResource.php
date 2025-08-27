<?php

namespace Admin\Http\Resources\Relation;

use Admin\Http\Resources\Relation\LinkResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RelationshipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'type' => $this->resource->getTable(),
        ];
    }

    public function with($request)
    {
        return [
            'links' => new LinkResource([
                'entity' => $request->route('entity'),
                'id' => $request->route('id'),
                'relation' => $request->route('relation'),
            ])
        ];
    }
}
