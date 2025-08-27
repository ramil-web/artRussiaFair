<?php

namespace App\Repositories\Program;

use App\Models\Program;
use App\Repositories\BaseRepository;

class ProgramRepository extends BaseRepository
{
    public function __construct(Program $model)
    {
        parent::__construct($model);
    }
}
