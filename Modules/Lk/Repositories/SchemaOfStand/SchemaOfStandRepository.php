<?php

namespace Lk\Repositories\SchemaOfStand;

use App\Exceptions\CustomException;
use App\Models\SchemaOfStand;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SchemaOfStandRepository extends BaseRepository
{
    public function __construct(SchemaOfStand $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Model|Builder
     * @throws CustomException
     */
    public function show(): Model|Builder
    {
        try {
            return $this->model->query()->firstOrFail();
        } catch (Throwable $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
