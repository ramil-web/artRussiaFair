<?php

namespace Lk\Repositories\MyTeam;

use App\Exceptions\CustomException;
use App\Models\Builder;
use App\Models\MyTeam;
use App\Models\StandRepresentative;
use App\Models\TimeSlotStart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response;

class MyTeamRepository extends BaseRepository
{
    public function __construct(MyTeam $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $userApplicationId
     * @return bool
     * @throws CustomException
     */
    public function checkMyTeam(int $userApplicationId): bool
    {
        try {
            return $this->model->query()
                ->where('user_application_id', $userApplicationId)
                ->exists();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function store(array $dataApp): Model
    {
        try {
            if ($this->slotReservation($dataApp['check_in']) && $this->slotReservation($dataApp['exit'])) {
                return $this->create($dataApp);
            }
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return int|bool
     * @throws CustomException
     */
    private function slotReservation(int $id): int|bool
    {
        try {
            $slot = TimeSlotStart::query()
                ->where('count', '<', 2)
                ->find($id);
            if ($slot->count > 0) {
                $data = ['count' => $slot->count + 1, 'status' => false];
            } else {
                $data = ['count' => $slot->count + 1];
            }
            return $slot->update($data);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $userApplicationId
     * @return Model|Builder
     * @throws CustomException
     */
    public function show(int $userApplicationId): Model|Builder
    {
        try {
            return $this->model->query()
                ->with(['builders', 'standRepresentatives', 'check_in', 'exit'])
                ->where('user_application_id', $userApplicationId)
                ->firstOrFail();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function delete(int $id): bool
    {
        try {
            $myTeam = $this->model
                ->query()
                ->findOrFail($id);
            $checkIn = $myTeam->check_in;
            $exit = $myTeam->exit;;

            /**
             * Before deleting my team , we free up the slots
             */
            if ($this->cancelSlotReservation($checkIn) && $this->cancelSlotReservation($exit)) {
                $myTeam->builders()->delete();
                $myTeam->standRepresentatives()->delete();
                return $this->forceDelete($myTeam);
            }
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool|int
     * @throws CustomException
     */
    private function cancelSlotReservation(int $id): bool|int
    {
        try {
            $slot = TimeSlotStart::query()
                ->findOrFail($id);
            return $slot->update(['count' => $slot->count - 1, 'status' => true]);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return Model
     * @throws CustomException
     */
    public function updateTeam(array $dataApp): Model
    {
        try {
            $team = $this->findById($dataApp['id']);

            /**
             * If a participant has changed the slot, we change the status and number of participants for the slot check_in
             */
            if ($team->check_in && array_key_exists('check_in', $dataApp) && $dataApp['check_in'] != $team->check_in) {
                $this->cancelSlotReservation($team->check_in);
                $this->slotReservation($dataApp['check_in']);
            }

            /**
             * If a participant has changed the slot, we change the status and number of participants for the slot exit
             */
            if ($team->exit && array_key_exists('exit', $dataApp) && $dataApp['exit'] != $team->exit) {
                $this->cancelSlotReservation($team->exit);
                $this->slotReservation($dataApp['exit']);
            }

            unset($dataApp['id']);
            $this->update($team, $dataApp);
            return $this->findById($team->id);
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param array $dataApp
     * @return bool
     * @throws CustomException
     */
    public function checkStandRepresentative(array $dataApp): bool
    {
        try {
            $team = $this->findById($dataApp['id']);
            $builders = StandRepresentative::where('user_application_id', $team->user_application_id)->count('id');
            return $dataApp['square'] < 30 && $builders > 3;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
