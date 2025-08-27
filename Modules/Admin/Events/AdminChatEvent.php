<?php

namespace Admin\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminChatEvent implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public mixed $user;
    public mixed $message;
    /**
     * @var mixed|string
     */
    public mixed $recipient_email;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $message, $email = '')
    {
        $this->user = $user;
        $this->message = $message;
        $this->recipient_email = $email;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): array|Channel
    {
        return ['admin-chat'];
    }
}
