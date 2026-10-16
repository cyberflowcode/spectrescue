<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels - SPECTRESCUE
|--------------------------------------------------------------------------
| Define authorization rules for WebSockets and real-time broadcasting.
*/

// User channel for targeted alerts and notifications
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public emergencies feed (for dispatchers, admins, citizens)
Broadcast::channel('emergencies', function ($user) {
    return true;
});

// Individual incident status update channel
Broadcast::channel('incident.{id}', function ($user, $id) {
    return true;
});
