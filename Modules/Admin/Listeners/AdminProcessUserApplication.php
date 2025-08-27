<?php

namespace Admin\Listeners;

use Admin\Events\AdminUserApplicationConfirmed;
use App\Models\Order;

class AdminProcessUserApplication
{
    public function handle(AdminUserApplicationConfirmed $event): void
    {
        Order::query()
        ->create([
            'user_application_id' => $event->userApplicationId,
            'status' => 'pending'
        ]);
    }
}
