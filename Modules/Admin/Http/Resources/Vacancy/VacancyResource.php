<?php

namespace Admin\Http\Resources\Vacancy;

use Admin\Http\Resources\BaseResource;

class VacancyResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'vacancy';
    protected string $namespace = 'Admin.';
}
