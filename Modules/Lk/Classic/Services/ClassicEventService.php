<?php

namespace Lk\Classic\Services;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lk\Classic\Repositories\ClassicEventRepository;

class ClassicEventService
{
    public function __construct(public ClassicEventRepository $repository)
    {
    }

    /**
     * @param string $type
     * @return Model|Builder
     * @throws CustomException
     */
    public function searchEvent(string $type): Model|Builder
    {
        return $this->repository->searchEvent($type);
    }
}
