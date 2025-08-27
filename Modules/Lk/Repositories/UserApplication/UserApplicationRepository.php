<?php

namespace Lk\Repositories\UserApplication;

use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use App\Models\Event;
use App\Models\UserApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserApplicationRepository extends BaseRepository
{

    public function __construct(UserApplication $model)
    {
        $this->model = $model;
        parent::__construct($model);
    }

    public function createFromArray(array $Data): Model
    {
        $locale = $Data['locate'] ?? app()->getLocale();

        $translate = config('transletable.userapp');
        foreach ($translate as $value) {
            $Data[$value] = [$locale => $Data[$value]];
        }
        return $this->model->query()->create($Data);
    }

    public function update($model, array $Data): bool
    {
        $locale = $Data['locate'] ?? app()->getLocale();
        $translate = config('transletable.userapp');

        foreach ($translate as $value) {
            $model->setTranslations($value, [$locale => $Data[$value]]);
            $Data = Arr::except($Data, [$value]);
        }

        return $model->update($Data);
    }

    /**
     * @param string $category
     * @return Model|Builder|null
     */
    public function getStatus(string $category): Model|Builder|null
    {
        return  $this->model
            ->query()
            ->where([
                'user_id' => Auth::id(),
                'active'  => true
            ])
            ->whereHas('event', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->select(['id', 'status'])
            ->latest()
            ->first();
    }

    /**
     * @param int $userApplicationId
     * @return true
     * @throws CustomException
     */
    public final function userAppConfirmed(int $userApplicationId): bool
    {
        try {
            $userApp = $this->findById($userApplicationId);
            if ($userApp->status !== 'confirmed') {
                throw new CustomException('Заявка не подтверждена', ResponseAlias::HTTP_FORBIDDEN);
            }
            return true;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param int $id
     * @return bool
     * @throws CustomException
     */
    public function checkIsNew(int $id): bool
    {
        try {
            $userApp = $this->model->query()->findOrFail($id);
            return match ($userApp->status) {
                AppStatusEnum::CONSIDERATION()->value => $this->setWaitingStatue($userApp),
                AppStatusEnum::WAITING_AFTER_EDIT()->value => $this->editable24Hours($userApp->updated_at),
                default => $this->editable24Hours($userApp->created_at)
            };
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param string $date
     * @return bool
     */
    private function editable24Hours(string $date): bool
    {
        $created = strtotime($date);
        $addOneDay = $created + (60 * 60 * 24);
        $nowTime = strtotime(now());
        return $nowTime < $addOneDay;
    }

    /**
     * @param Model|Collection|Builder|array|null $userApp
     * @return int|bool
     * @throws CustomException
     */
    private function setWaitingStatue(Model|Collection|Builder|array|null $userApp): int|bool
    {
        try {
            $userApp->update([
                'status'     => AppStatusEnum::WAITING_AFTER_EDIT()->value,
                'updated_at' => now()
            ]);
            return true;
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $eventId
     * @return Collection|array
     * @throws CustomException
     */
    public function getUserAppByEventCategory(int $eventId): Collection|array
    {
        try {
            $event = Event::query()
                ->select(['id', 'category'])
                ->find($eventId);

            return UserApplication::query()
                ->where([
                    'user_id' => Auth::id(),
                    'active'  => true
                ])
                ->whereHas('event', function ($query) use ($event) {
                    $query->where('category', $event->category);
                })
                ->select(['id', 'status', 'active'])
                ->get();
        } catch (QueryException $e) {
            throw new CustomException($e->getMessage(), $e->getCode());
        }
    }
}
