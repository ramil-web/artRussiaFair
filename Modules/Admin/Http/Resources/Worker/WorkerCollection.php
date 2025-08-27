<?php

namespace  Admin\Http\Resources\Worker;


use Admin\Http\Resources\BaseCollection;


class WorkerCollection extends BaseCollection
{
    protected string $type = 'worker';
    protected string $namespace = 'Admin.';

    public function toArray($request):array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
