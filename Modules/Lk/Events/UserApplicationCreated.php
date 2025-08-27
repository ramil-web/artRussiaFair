<?php

namespace Lk\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserApplicationCreated
{
    use Dispatchable, SerializesModels;

    public int $userApplicationId;

    /**
     * @param int $userApplicationId
     */
    public function __construct(int $userApplicationId)
    {
        $this->userApplicationId = $userApplicationId;
    }
}
