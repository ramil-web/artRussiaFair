<?php

namespace Lk\Repositories\Person;

use App\Models\Person;
use Illuminate\Database\Eloquent\Model;
use Lk\Repositories\BaseRepository;

class PersonRepository extends BaseRepository
{

    public function __construct(Person $model)
    {
        $this->model = $model;
    }

    public function create(array $Data): Model
    {
        $locale = $Data['locate'] ?? app()->getLocale();

        $translate = config('transletable.person');
        foreach ($translate as $value) {
            $Data[$value] = [$locale => $Data[$value]];
        }

        return $this->model->create($Data);
    }

}
