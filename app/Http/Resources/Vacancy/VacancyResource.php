<?php

namespace App\Http\Resources\Vacancy;

use App\Http\Resources\BaseResource;

class VacancyResource extends BaseResource
{
    protected array $relationships = [];
    protected string $type = 'vacancy';
    protected string $namespace='';
}
