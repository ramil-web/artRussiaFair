<?php

namespace Admin\Classic\Http\Resources\ClassicEvent;

use Admin\Http\Resources\BaseCollection;

class ClassicEventCollection extends BaseCollection
{
    protected string $type = 'classic-event';
    protected string $namespace = 'Admin.';

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection
        ];
    }
}
