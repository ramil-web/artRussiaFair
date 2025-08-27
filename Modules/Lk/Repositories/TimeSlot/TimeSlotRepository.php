<?php


namespace Lk\Repositories\TimeSlot;

use App\Models\TimeSlotStart;
use Lk\Repositories\BaseRepository;
use Spatie\QueryBuilder\QueryBuilder;

class TimeSlotRepository extends BaseRepository
{
    public function __construct(TimeSlotStart $model)
    {
        parent::__construct($model);
    }

    public function getAll(
        array $where,
        array $allowedFields,
        array $allowedSorts,
        array $allowedFilters,
        int   $perPage = null
    )
    {
        $query = QueryBuilder::for($this->model)
            ->where($where)
            ->allowedFilters($allowedFilters)
            ->select('id', 'date', 'interval_times', 'action', 'event_id')
            ->allowedFields($allowedFields)
            ->defaultSort('id')
            ->allowedSorts($allowedSorts);

        return $perPage !== null
            ? $query->jsonPaginate($perPage)
            : $query->get();
    }
}
