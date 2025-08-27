<?php


namespace Admin\Repositories\Manager;


use Admin\Repositories\BaseRepository;
use App\Models\ManagerProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;


class ManagerProfileRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(ManagerProfile $model)
    {
        $this->model = $model;
    }

    public function create(array $Data): Model
    {
        $locale = $Data['locate'] ?? app()->getLocale();

        $translate = config('transletable.managerprofile');
        foreach ($translate as $value) {
            $Data[$value] = [$locale => $Data[$value]];
        }
        return $this->model->updateOrCreate(['user_id' => $Data['user_id']], $Data);
    }

    public function update(Model $model, array $Data): bool
    {

        $locale = $Data['locate'] ?? app()->getLocale();
        $translate = config('transletable.managerprofile');
        foreach ($translate as $value) {
            $model->setTranslations($value, [$locale => $Data[$value]]);
            Arr::except($Data, $value);
        }
        return $model->update($Data);
    }
}
