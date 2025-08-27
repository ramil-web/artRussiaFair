<?php

namespace Lk\Repositories\Order;

use App\Models\Order;
use Lk\Repositories\BaseRepository;

class OrderRepository extends BaseRepository
{

    public function __construct(Order $model)
    {
        parent::__construct($model);
    }
}
