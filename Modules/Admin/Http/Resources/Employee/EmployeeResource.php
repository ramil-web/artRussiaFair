<?php

namespace Admin\Http\Resources\Employee;

use Admin\Http\Resources\BaseResource;

class EmployeeResource extends BaseResource
{
    protected array $relationships = [
        'user_applications'
    ];
    protected string $type = 'employee';
    protected string $namespace = 'Admin.';
}
