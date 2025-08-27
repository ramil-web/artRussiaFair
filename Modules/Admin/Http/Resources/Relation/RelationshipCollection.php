<?php

namespace Admin\Http\Resources\Relation;

use Admin\Http\Resources\Relation\LinkResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class RelationshipCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function with($request)
    {
        return [
            'links' => new LinkResource([
                'relation' => $request->route('relation'),
                'id' => $request->route('id'),
                'entity' => $request->route('entity'),
            ])
        ];
    }

}
