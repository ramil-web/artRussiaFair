<?php

namespace Lk\Http\Resources\Employee;

use Lk\Http\Resources\BaseCollection;


/** @see \App\Models\UserApplication */
class EmployeeCollection extends BaseCollection
{
    protected string $type = 'employee';
    protected string $namespace = 'lk.';

    /**
     * @param $request
     * @return array
     */
    public final function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
