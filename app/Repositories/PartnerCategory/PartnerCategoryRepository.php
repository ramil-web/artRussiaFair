<?php

namespace App\Repositories\PartnerCategory;

use Admin\Repositories\BaseRepository;
use App\Models\PartnerCategory;

class PartnerCategoryRepository extends BaseRepository
{
    public function __construct(PartnerCategory $model)
    {
        parent::__construct($model);
    }
}
