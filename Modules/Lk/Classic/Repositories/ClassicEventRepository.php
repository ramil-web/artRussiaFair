<?php

namespace Lk\Classic\Repositories;

use App\Exceptions\CustomException;
use App\Models\ClassicEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;

class ClassicEventRepository extends BaseRepository
{
    public function __construct(ClassicEvent $model)
    {
        parent::__construct($model);
    }

    /**
     * @param string $type
     * @return Model|Builder
     * @throws CustomException
     */
    public function searchEvent(string $type): Model|Builder
    {
        try {
            $currentDate = Carbon::parse(Carbon::now())->format('Y-m-d H:i:s');
            return $this->model
                ->query()
                ->where([
                    'status'     => true,
                ])
                ->where('end_accepting_applications', '>', $currentDate)
                ->whereRaw("event_type ~ '^main[0-9]{4}$'")
                ->firstOrFail();
        } catch (QueryException $e) {
            throw  new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
