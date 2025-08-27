<?php

namespace Admin\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminUserApplicationConfirmed
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
