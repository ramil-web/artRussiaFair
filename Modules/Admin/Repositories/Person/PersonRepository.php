<?php

namespace Admin\Repositories\Person;

use Admin\Repositories\BaseRepository;
use App\Models\Person;

class PersonRepository extends BaseRepository
{
    public function __construct(Person $model)
    {
        parent::__construct($model);
    }

}
