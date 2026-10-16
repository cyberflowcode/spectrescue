<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;

    /**
     * Create a new event instance when an incident status or assignment changes.
     */
    public function __construct(Incident $incident)
    {
        $incident->loadMissing(['latestAssignment.responder.user', 'responseReport']);

        $responder = null;
        if ($incident->latestAssignment && $incident->latestAssignment->responder) {
            $responder = [
                'name'           => $incident->latestAssignment->responder->user->name,
                'specialization' => $incident->latestAssignment->responder->specialization,
                'badge_number'   => $incident->latestAssignment->responder->badge_number,
                'status'         => $incident->latestAssignment->status,
            ];
        }

        $this->incident = [
            'id'               => $incident->id,
            'tracking_code'    => $incident->tracking_code,
            'title'            => $incident->title,
            'threat_level'     => $incident->threat_level,
            'status'           => $incident->status,
            'responder'        => $responder,
            'has_report'       => $incident->responseReport !== null,
        ];
    }

    /**
     * The channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('emergencies'),
            new Channel('incident.' . $this->incident['id']),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'IncidentStatusUpdated';
    }
}
