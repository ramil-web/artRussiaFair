<?php

namespace Admin\Repositories\Order;

use Admin\Repositories\BaseRepository;
use App\Exceptions\CustomException;
use App\Models\Order;
use App\Models\TimeSlotStart;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderRepository extends BaseRepository
{

    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    /**
     * @param array $withRelation
     * @param array $allowedFilters
     * @param array $allowedFields
     * @param array $allowedIncludes
     * @param array $allowedSorts
     * @param int|null $perPage
     * @return Collection|QueryBuilder[]
     * @throws CustomException
     */
    public function getAllByFilters(
        array $withRelation = [],
        array $allowedFilters = [],
        array $allowedFields = [],
        array $allowedIncludes = [],
        array $allowedSorts = [],
        int   $perPage = null,
    )
    {
        try {
            $query = QueryBuilder::for($this->model)
                ->with($withRelation)
                ->allowedFilters($allowedFilters)
                ->allowedFields($allowedFields)
                ->allowedIncludes($allowedIncludes)
                ->defaultSort('id')
                ->allowedSorts($allowedSorts);

            return $perPage !== null
                ? $query->jsonPaginate($perPage)
                : $query->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @param int $id
     * @param array $data
     * @throws CustomException
     */
    public function chekSlotCount(int $id, array $data)
    {

        /**
         * Получае сушествуйщий time_slot_start_id из обновляемой заявки
         */
        $userAppSlotId = Order::query()
            ->findOrFail($id)
            ->time_slot_start_id;

        /**
         * Проверяем что приходит или не приходит time_slot_start_id
         */
        $slotId = array_key_exists('time_slot_start_id', $data) && $data['time_slot_start_id'] > 0 ? $data['time_slot_start_id'] : null;

        if (!$userAppSlotId && $slotId) {
            $this->addTimeSlotCount($slotId);
        } else if ($userAppSlotId && $slotId) {
            $this->removeTimeSlotCount($userAppSlotId);
            $this->addTimeSlotCount($slotId);
        } else if ($userAppSlotId && !$slotId) {
            $this->removeTimeSlotCount($userAppSlotId);
        }
    }


    /**
     * @param int $slotId
     * @return bool|int
     * @throws CustomException
     */
    public function removeTimeSlotCount(int $slotId)
    {
        try {
            $timeSlot = TimeSlotStart::query()
                ->find($slotId);
            if ($timeSlot) {
                if ($timeSlot->count > 0) {
                    $timeSlot->decrement('count');
                }
                return $timeSlot->update(['status' => true]);
            }
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @param int $slotId
     * @return bool|int
     * @throws CustomException
     */
    public function addTimeSlotCount(int $slotId)
    {
        try {
            $slot = TimeSlotStart::query()
                ->where('status', true)
                ->find($slotId);
            if (!$slot) {
                throw new CustomException("Слот с ID $slotId не найден!", ResponseAlias::HTTP_NOT_FOUND);
            }
            $slot->increment('count');
            if ($slot->count >= 2) {
                return $slot->update(['status' => false]);
            }
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_NOT_FOUND);
        }
    }
}
