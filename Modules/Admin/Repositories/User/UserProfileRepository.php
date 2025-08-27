<?php


namespace Admin\Repositories\User;


use Admin\Repositories\BaseRepository;
use App\Models\UserProfile;
use Arr;
use Illuminate\Database\Eloquent\Model;


class UserProfileRepository extends BaseRepository
{
    protected Model $model;

    public function __construct(UserProfile $model)
    {
        $this->model = $model;
    }

    public function create(array $Data): Model
    {
        return $this->model->updateOrCreate(['user_id' => $Data['user_id']], $Data);
    }

    public function update(Model $model, array $Data): bool
    {

        if(Arr::exists($Data, 'name')){
            $model->setTranslations('name',$Data['name']);
            Arr::except($Data, ['name']);
        }
        if(Arr::exists($Data, 'surname')){
            $model->setTranslations('surname',$Data['surname']);
            Arr::except($Data, ['surname']);
        }
        if(Arr::exists($Data, 'city')){
            $model->setTranslations('city',$Data['city']);
            Arr::except($Data, ['city']);
        }
        return $model->update($Data);
    }
}
