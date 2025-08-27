<?php

namespace Lk\Repositories\Event;

use App\Exceptions\CustomException;
use App\Models\Event;
use App\Models\TimeSlotStart;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;

class EventRepository extends BaseRepository
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $type
     * @param string $category
     * @return Builder|Model|array
     * @throws CustomException
     */
    public function searchEvent(string $type, string $category): Builder|Model|array
    {
        try {
            $currentDate = Carbon::parse(Carbon::now())->format('Y-m-d H:i:s');
            $response =  $this->model
                ->query()
                ->where([
                    'type'   => $type,
                    'status' => true,
                ])
                ->where('end_accepting_applications', '>', $currentDate)
                ->where('category', $category)
                ->whereRaw("event_type ~ '^main[0-9]{4}$'")
                ->first();
            return $response ?? [];
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return Collection|array
     * @throws CustomException
     */
    public function slots(int $id): Collection|array
    {
        try {
            $query = TimeSlotStart::query()
                ->where([
                    'event_id' => $id,
                    'status'   => true
                ]);
            if (!$query->exists()) {
                return [];
            }
            return $query->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
