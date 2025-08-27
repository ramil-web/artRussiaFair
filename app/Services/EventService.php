<?php

namespace App\Services;

use App\Exceptions\CustomException;
use App\Models\Event;
use App\Repositories\Event\EventRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EventService
{

    public function __construct(
        protected EventRepository $eventRepository
    )
    {
    }

    /**
     * @param int $id
     * @return Model|null
     * @throws CustomException
     */
    public function show(int $id): ?Model
    {
        return $this->eventRepository->get($id);
    }

    /**
     * @param array $appData
     * @return Collection|array
     * @throws CustomException
     */
    public function list(array $appData): Collection|array
    {
        return $this->eventRepository->list($appData);
    }
}
