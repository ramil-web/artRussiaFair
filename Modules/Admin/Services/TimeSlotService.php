<?php


namespace Admin\Services;

use Admin\Repositories\TimeSlot\TimeSlotRepository;
use App\Exceptions\CustomException;
use App\Models\TimeSlotStart;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class TimeSlotService
{
    public function __construct(public TimeSlotRepository $repository)
    {
    }

    /**
     * @param array $dataApp
     * @return string[]
     * @throws Throwable
     */
    public function updateOrCreate(array $dataApp): array
    {
        $interval = CarbonInterval::minute($dataApp['interval']);
        $period = new CarbonPeriod(
            $this->carbonCreateFormat('Y-m-d H:i', $dataApp['begin']),
            $interval,
            $this->carbonCreateFormat('Y-m-d H:i', $dataApp['end']),
        );
        try {
            $currentIds = [];
            foreach ($period as $dt) {
                $currentIds[] = TimeSlotStart::query()->updateOrCreate(
                    ['date' => $dt->format("Y-m-d"), 'interval_times' => $dt->format("H:i"), 'action' => $dataApp['action']],
                    ['date' => $dt->format("Y-m-d"), 'interval_times' => $dt->format("H:i"), 'action' => $dataApp['action'], 'event_id' => $dataApp['event_id']]
                );
            }
            $response = TimeSlotStart::query()
                ->whereIn('id', array_column($currentIds, 'id'))
                ->get()
                ->toArray();
        } catch (QueryException $e) {
            throw new CustomException("The slot has not been created", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        /**
         * Временно закомментировал отправку писем, пока не опредилили содержимое для мисма
         */
        return $response;
    }

    /**
     * @param array $dataApp
     * @return array
     * @throws CustomException
     */
    public function update(array $dataApp): array
    {
       return $this->repository->updateInterval($dataApp);
    }

    /**
     * @param int $eventId
     * @return array
     * @throws CustomException
     */
    public function getTimeSlotIntervals(int $eventId): array
    {
        return $this->repository->getTimeSlotIntervals($eventId);
    }

    /**
     * @param $begin
     * @param $end
     * @param $action
     * @return array
     * Если есть слот которого не будет в новом временном отрезке, но он занят, получаем email для отправки письма
     * @throws CustomException
     * @throws Throwable
     */
    private function getBusySlotsUserEmails($begin, $end, $action): array
    {
        $from = $this->carbonCreateFormat('Y-m-d H:i', $begin);
        $to = $this->carbonCreateFormat('Y-m-d H:i', $end);
        try {
            $query = DB::table('time_slot_start as ts')
                ->whereBetween('date', [$from->format("Y-m-d"), $to->format("Y-m-d")])
                ->whereBetween('interval_times', [$from->format("H:i"), $to->format("H:i")])
                ->leftJoin('my_teams as mt', "mt.$action", 'ts.id')
                ->leftJoin('user_applications as uap', 'mt.user_application_id', 'uap.id')
                ->where('ts.status', true)
                ->get();

            return $query->toArray();
        } catch (QueryException $e) {
            DB::rollBack();
            throw new CustomException("Слот не был создан", ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param string $date
     * @param string $format
     * @return Carbon|false
     */
    public function carbonCreateFormat(string $format, string $date): bool|Carbon
    {
        return Carbon::createFromFormat($format, $date);
    }
}
