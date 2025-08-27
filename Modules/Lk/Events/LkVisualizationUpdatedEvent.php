<?php

namespace Lk\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LkVisualizationUpdatedEvent implements ShouldBroadcast
{

    public mixed $visualization;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($visualization)
    {
        $this->visualization = $visualization;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): array|Channel
    {
        return ['lk-visualization'];
    }
}
