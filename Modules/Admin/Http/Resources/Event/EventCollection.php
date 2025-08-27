<?php

namespace Admin\Http\Resources\Event;

use Admin\Http\Resources\BaseCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Events */
class EventCollection extends BaseCollection
{
    protected string $type = 'events';
    protected string $namespace = 'Admin.';

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
