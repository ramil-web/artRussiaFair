<?php

namespace App\Http\Resources\Vacancy;

use App\Http\Resources\BaseCollection;

class VacancyCollection extends BaseCollection
{
    protected string $type = 'vacancy';
    protected string $namespace = '';

    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {

        return [
            'data' => $this->collection,
        ];
    }
}
