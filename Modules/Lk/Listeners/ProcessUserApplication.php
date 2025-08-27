<?php

namespace Lk\Listeners;

use App\Models\Order;
use Lk\Events\UserApplicationCreated;

class ProcessUserApplication
{
    public function handle(UserApplicationCreated $event)
    {
        Order::create([
            'user_application_id' => $event->userApplicationId,
            'status' => 'pending'
        ]);
    }
}
