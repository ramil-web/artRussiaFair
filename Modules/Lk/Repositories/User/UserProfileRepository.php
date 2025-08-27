<?php


namespace Lk\Repositories\User;


use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lk\Repositories\BaseRepository;


class UserProfileRepository extends BaseRepository
{

    public function __construct(UserProfile $model)
    {
        parent::__construct($model);
    }

    public function create(array $Data): Model
    {
        return $this->model->updateOrCreate(['user_id' => $Data['user_id']], $Data);
    }

    public function update(Model $model, array $Data): bool
    {

        $locale = $Data['locate'] ?? app()->getLocale();
        $translate = config('transletable.userprofile');
        foreach ($translate as $value) {
            $model->setTranslations($value, [$locale => $Data[$value]]);
            Arr::except($Data, $value);
        }
        return $model->update($Data);
    }
}
