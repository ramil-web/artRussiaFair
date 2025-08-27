<?php

namespace Lk\Services;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\Event\EventRepository;

class EventService
{

    public function __construct(protected EventRepository $eventRepository)
    {
    }


    /**
     * @param string $type
     * @param string $category
     * @return Builder|Model|array
     * @throws CustomException
     */
    public function searchEvent(string $type, string $category): Builder|Model|array
    {
        return $this->eventRepository->searchEvent($type, $category);
    }

    /**
     * @param int $id
     * @return Collection|array
     * @throws CustomException
     */
    public function slots(int $id): Collection|array
    {
        return $this->eventRepository->slots($id);
    }
}
