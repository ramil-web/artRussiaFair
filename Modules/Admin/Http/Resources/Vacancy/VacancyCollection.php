<?php

namespace Admin\Http\Resources\Vacancy;

use Admin\Http\Resources\BaseCollection;

class VacancyCollection extends BaseCollection
{
    protected string $type = 'vacancy';
    protected string $namespace = 'Admin.';

    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
