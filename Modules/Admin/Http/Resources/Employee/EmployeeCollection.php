<?php

namespace  Admin\Http\Resources\Employee;


use Admin\Http\Resources\BaseCollection;


class EmployeeCollection extends BaseCollection
{
    protected string $type = 'employee';
    protected string $namespace = 'Admin.';

    public function toArray($request):array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
