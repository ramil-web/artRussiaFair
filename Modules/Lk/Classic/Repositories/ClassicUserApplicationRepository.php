<?php

namespace Lk\Classic\Repositories;

use App\Enums\AppStatusEnum;
use App\Exceptions\CustomException;
use App\Models\ClassicUserApplication;
use App\Models\ClassicUserApplicationImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Lk\Repositories\BaseRepository;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ClassicUserApplicationRepository extends BaseRepository
{
    public function __construct(ClassicUserApplication $model, public ClassicUserApplicationImage $appImage)
    {
        parent::__construct($model);
    }

    /**
     * @param array $Data
     * @return Model
     */
    public function createFromArray(array $Data): Model
    {
        $locale = $Data['locate'] ?? app()->getLocale();
        $translate = config('transletable.userapp');
        foreach ($translate as $value) {
            $Data[$value] = [$locale => $Data[$value]];
        }
        return $this->model->query()->firstOrCreate([
            'number' => $Data['number']
        ], $Data);
    }

    /**
     * @param array $Data
     * @return Model
     */
    public function create(array $Data): Model
    {
        return $this->appImage->query()
            ->updateOrCreate([
                'url'                         => $Data['url'],
                'classic_user_application_id' => $Data['classic_user_application_id']
            ],
                $Data);
    }

    /**
     * @param int $id
     * @param array $newImage
     * @return void
     */
    public function cleanImage(int $id, array $newImage): void
    {
        $images = $this->appImage->query()->where('classic_user_application_id', $id)->get()->toArray();
        foreach ($images as $image) {
            if (!in_array($image['url'], $newImage, true)) {
                $this->appImage->query()->whereId($image['id'])->delete();
            }
        }

    }

    /**
     * @param array $Data
     * @return Model|Builder
     */
    public function updateImage(array $Data): Model|Builder
    {
        return $this->appImage->query()->updateOrCreate(['url' => $Data['url']], $Data);
    }

    /**
     * @param int $id
     * @return bool|int
     * @throws CustomException
     */
    public function checkIsNew(int $id): bool|int
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
     * @return Model|Builder|null
     */
    public function getStatus(): Model|Builder|null
    {
        return $this->model
            ->query()
            ->select(['status', 'id'])
            ->where('user_id', auth()->id())
            ->latest()
            ->first();
    }
}
