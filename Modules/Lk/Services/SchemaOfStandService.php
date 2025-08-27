<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\SchemaOfStand\SchemaOfStandRepository;

class SchemaOfStandService
{
    public function __construct(public SchemaOfStandRepository $repository)
    {
    }

    /**
     * @return Model|Builder
     * @throws CustomException
     */
    public function show(): Model|Builder
    {
        return $this->repository->show();
    }
}
