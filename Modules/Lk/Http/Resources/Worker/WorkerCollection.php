<?php

namespace Lk\Http\Resources\Worker;

use Lk\Http\Resources\BaseCollection;


/** @see \App\Models\UserApplication */
class WorkerCollection extends BaseCollection
{
    protected string $type = 'worker';
    protected string $namespace = 'lk.';

    public final function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
