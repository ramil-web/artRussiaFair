<?php


namespace Lk\Services;

use App\Enums\TimeSlotEnum;
use App\Models\TimeSlotStart;
use Illuminate\Database\Eloquent\Collection;
use Lk\Repositories\TimeSlot\TimeSlotRepository;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TimeSlotService
{


    public function __construct(
        public TimeSlotStart $timeSlotStart,
        public TimeSlotRepository $timeSlotRepository
    ){}

    /**
     * @param array $dataApp
     * @param TimeSlotEnum $type
     * @return Collection|QueryBuilder[]
     */
    public function getSlots(array $dataApp, TimeSlotEnum $type): Collection|QueryBuilder
    {
        $allowedFields = [
            'id',
            'date',
            'interval_times',
            'action',
            'status',
            'event_id'
        ];
        $allowedSorts = ['event_id', 'date'];
        $allowedFilters = [
            AllowedFilter::exact('id'),
            AllowedFilter::exact('event_id'),
            AllowedFilter::exact('date'),
        ];

        $where = ['action' => $type, 'status' => true];
        $perPage = array_key_exists('per_page', $dataApp) ? $dataApp['per_page'] :  null;
        return $this->timeSlotRepository->getAll(
            $where,
            $allowedFields,
            $allowedSorts,
            $allowedFilters,
            $perPage
        );
    }
}
