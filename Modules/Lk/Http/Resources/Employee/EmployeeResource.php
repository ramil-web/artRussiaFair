<?php

namespace Lk\Http\Resources\Employee;

use Lk\Http\Resources\BaseResource;

class EmployeeResource extends BaseResource
{
    protected array $relationships = [
        'user_applications'
    ];
    protected string $type = 'employee';
    protected string $namespace='lk.';
}
