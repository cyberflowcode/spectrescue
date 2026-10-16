<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    /**
     * Create a new event instance when an internal notification is delivered.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = [
            'id'      => $notification->id,
            'user_id' => $notification->user_id,
            'title'   => $notification->title,
            'message' => $notification->message,
            'link'    => $notification->link,
        ];
    }

    /**
     * The channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('user.' . $this->notification['user_id']),
            new Channel('notifications'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'NotificationSent';
    }
}
