<?php

namespace App\Repositories\Partner;

use App\Models\Partner;
use App\Repositories\BaseRepository;

class PartnerRepository extends BaseRepository
{
    const DESC = 'desc';

    public function __construct(Partner $model)
    {
        parent::__construct($model);
    }

}
