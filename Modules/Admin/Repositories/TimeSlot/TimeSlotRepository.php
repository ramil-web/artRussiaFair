<?php


namespace Admin\Repositories\TimeSlot;


use Admin\Events\AdminChatEvent;
use Admin\Repositories\BaseRepository;
use Admin\Services\ChatService;
use App\Exceptions\CustomException;
use App\Models\TimeSlotStart;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class TimeSlotRepository extends BaseRepository
{

    public function __construct(TimeSlotStart $model, protected ChatService $chatService)
    {
        parent::__construct($model);
    }

    /**
     * @param int $eventId
     * @return array
     * @throws CustomException
     */
    public function getTimeSlotIntervals(int $eventId): array
    {
        try {

            $whereCheckIn = ['action' => 'check_in', 'event_id' => $eventId];

            $minDateCheckIn = DB::table('time_slot_start')
                ->where($whereCheckIn)
                ->min('date');

            $maxDateCheckIn = DB::table('time_slot_start')
                ->where($whereCheckIn)
                ->max('date');

            $minCheckIn = DB::table('time_slot_start')
                ->where('date', $minDateCheckIn)
                ->where($whereCheckIn)
                ->select('action', DB::raw('MIN(date) as min_date'), DB::raw('MIN(interval_times) as min_interval_time'))
                ->groupBy('action');

            $maxCheckIn = DB::table('time_slot_start')
                ->where('date', $maxDateCheckIn)
                ->where($whereCheckIn)
                ->select('action', DB::raw('MAX(date) as max_date'), DB::raw('MAX(interval_times) as max_interval_time'))
                ->groupBy('action');


            $whereExit = ['action' => 'exit', 'event_id' => $eventId];
            $minDateExit = DB::table('time_slot_start')
                ->where($whereExit)
                ->min('date');

            $maxDateExit = DB::table('time_slot_start')
                ->where($whereExit)
                ->max('date');

            $minExit = DB::table('time_slot_start')
                ->where('date', $minDateExit)
                ->where($whereExit)
                ->select('action', DB::raw('MIN(date) as min_date'), DB::raw('MIN(interval_times) as min_interval_time'))
                ->groupBy('action');

            $maxExit = DB::table('time_slot_start')
                ->where('date', $maxDateExit)
                ->where($whereExit)
                ->select('action', DB::raw('MAX(date) as max_date'), DB::raw('MAX(interval_times) as max_interval_time'))
                ->groupBy('action');


            $response = $minCheckIn
                ->unionAll($maxCheckIn)
                ->unionAll($minExit)
                ->unionAll($maxExit)
                ->get();

            if (count($response) < 1) {
                return [];
            }


            return [
                'check_in' => [
                    'begin' => $response[0]->min_date . " " . $response[0]->min_interval_time,
                    'end'   => $response[1]->min_date . " " . $response[1]->min_interval_time,
                ],
                'exit'     => [
                    'begin' => $response[2]->min_date . " " . $response[2]->min_interval_time,
                    'end'   => $response[3]->min_date . " " . $response[3]->min_interval_time,
                ],
            ];
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), 500);
        }
    }


    /**
     * @throws CustomException
     */
    public function updateInterval(array $data): array
    {
        try {
            $response = [];
            if (array_key_exists('check_in', $data)) {
                $response = $this->updateSlots($data['event_id'], $data['check_in'], 'check_in', 30);
            }

            if (array_key_exists('exit', $data)) {
                $response = $this->updateSlots($data['event_id'], $data['exit'], 'exit', 15);
            }

            return $response;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Builder $slots
     * @return void
     * @throws CustomException
     */
    private function sendMessageToUsers(Builder $slots): void
    {
        try {
            $adminId = Auth::id();
            $adminModel = User::query()->find($adminId);
            $deleted = $slots
                ->get(['id', 'count'])->toArray();

            foreach ($deleted as $item) {
                if ($item->count > 0) {
                    foreach ($this->recipientEmail($item) as $user) {
                        $message = "Уважаемый участник Art Russia, даты и время монтажа/демонтажа были скорректированы.
                                   Просим зайти в личный кабинет для выбора и подтверждения подходящего вам слота.";
                        $message = $this->chatService->createMessage(['message' => $message, 'user_id' => $user->id]);
                        broadcast(new AdminChatEvent($adminModel, $message, $user->email))->toOthers();
                    }
                }
            }
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param mixed $item
     * @return array
     * @throws CustomException
     */
    private function recipientEmail(mixed $item): array
    {
        try {
            return DB::table("my_teams as mt")
                ->where('mt.check_in', $item->id)
                ->orWhere('mt.exit', $item->id)
                ->leftJoin('user_applications as ua', 'ua.id', 'mt.user_application_id')
                ->leftJoin('users as u', 'u.id', 'ua.user_id')
                ->get(['u.email', 'u.id'])->toArray();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $eventId
     * @param mixed $slots
     * @param $action
     * @return array|array[]
     * @throws CustomException
     */
    private function updateSlots(int $eventId, mixed $slots, string $action, int $interval): array
    {
        try {
            $beginDate = Carbon::parse($slots['begin']);
            $endDate = Carbon::parse($slots['end']);

            /**
             * Removing slots out of range
             */
            $slots = DB::table('time_slot_start')
                ->where([
                    'action'   => $action,
                    'event_id' => $eventId
                ])
                ->where(function ($query) use ($beginDate, $endDate) {
                    $query->where('date', '<', $beginDate->toDateString())
                        ->orWhere('date', '>', $endDate->toDateString())
                        ->orWhere(function ($query) use ($beginDate, $endDate) {
                            $query->whereNotBetween('interval_times', [
                                $beginDate->toTimeString(),
                                $endDate->toTimeString(),
                            ]);
                        });
                });

            /**
             * If we have deleted slots that were already occupied by the participants,
             * we start sending messages to the participants with information about the deleted slot
             */
            $this->sendMessageToUsers($slots);

            $slots->delete();

            /**
             * Adding missing slots
             */

            $start = clone $beginDate;

            while ($start->lte($endDate)) {

                /**
                 * Check the slot availability
                 */
                $exists = DB::table('time_slot_start')
                    ->where([
                        'action'   => $action,
                        'event_id' => $eventId
                    ])
                    ->where('date', $start->toDateString())
                    ->where('interval_times', $start->toTimeString())
                    ->where('action', $action)
                    ->exists();

                if (!$exists) {
                    DB::table('time_slot_start')->insert([
                        'date'           => $start->toDateString(),
                        'interval_times' => $start->toTimeString(),
                        'event_id'       => $eventId,
                        'action'         => $action,
                    ]);
                }

                /**
                 * Increasing the interval depending on the action
                 */
                $start->addMinutes($interval);
            }
            return $this->getTimeSlotIntervals($eventId);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }


}
