<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewEmergencyReported implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;

    /**
     * Create a new event instance when a citizen reports an emergency.
     */
    public function __construct(Incident $incident)
    {
        $this->incident = [
            'id'               => $incident->id,
            'tracking_code'    => $incident->tracking_code,
            'title'            => $incident->title,
            'threat_level'     => $incident->threat_level,
            'status'           => $incident->status,
            'location_city'    => $incident->location_city,
            'location_address' => $incident->location_address,
            'created_at_human' => $incident->created_at->diffForHumans(),
        ];
    }

    /**
     * The channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('emergencies'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'NewEmergencyReported';
    }
}
