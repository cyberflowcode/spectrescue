<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Responder;
use App\Models\Assignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatcherController extends Controller
{
    /**
     * Dispatcher Dashboard: overview of pending emergencies, active missions, and available responders.
     */
    public function dashboard(Request $request)
    {
        // 1. Pending incidents waiting for dispatch
        $pendingIncidents = Incident::with(['citizen', 'incidentType'])
            ->where('status', 'Pending')
            ->latest()
            ->get();

        // 2. Currently active emergencies (Dispatched or Responding)
        $activeIncidents = Incident::with(['citizen', 'incidentType', 'latestAssignment.responder.user'])
            ->whereIn('status', ['Dispatched', 'Responding'])
            ->latest()
            ->get();

        // 3. Approved responders ready for emergency assignment (only those not currently deployed on an active mission)
        $availableResponders = Responder::with('user')
            ->where('verification_status', 'approved')
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('status', ['assigned', 'accepted'])
                  ->whereHas('incident', function ($inc) {
                      $inc->whereIn('status', ['Dispatched', 'Responding']);
                  });
            })
            ->get();

        // 4. Quick stats
        $stats = [
            'pending_count'  => $pendingIncidents->count(),
            'active_count'   => $activeIncidents->count(),
            'critical_count' => Incident::whereIn('status', ['Pending', 'Dispatched', 'Responding'])->where('threat_level', 'Critical')->count(),
            'responders_available' => $availableResponders->count(),
        ];

        return view('dispatcher.dashboard', compact('pendingIncidents', 'activeIncidents', 'availableResponders', 'stats'));
    }

    /**
     * Show detailed dispatch console for an incident.
     */
    public function showIncident(Incident $incident)
    {
        $incident->load(['citizen', 'incidentType', 'evidence', 'assignments.responder.user', 'responseReport.responder.user']);

        // Check if there is an active specialist already deployed to this incident
        $activeAssignment = $incident->assignments()
            ->whereIn('status', ['assigned', 'accepted'])
            ->latest()
            ->first();

        // Fetch only verified responders who are NOT currently deployed on any active mission
        $availableResponders = Responder::with('user')
            ->where('verification_status', 'approved')
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('status', ['assigned', 'accepted'])
                  ->whereHas('incident', function ($inc) {
                      $inc->whereIn('status', ['Dispatched', 'Responding']);
                  });
            })
            ->get();

        return view('dispatcher.incidents.show', compact('incident', 'availableResponders', 'activeAssignment'));
    }

    /**
     * Assign a verified responder to an emergency incident.
     */
    public function assignResponder(Request $request, Incident $incident)
    {
        // 1. Check if incident is already resolved
        if ($incident->status === 'Resolved') {
            return back()->with('error', 'This emergency incident has already been resolved and closed.');
        }

        // 2. Prevent assigning if this incident already has an active specialist deployed
        $existingActiveAssignment = $incident->assignments()
            ->whereIn('status', ['assigned', 'accepted'])
            ->first();

        if ($existingActiveAssignment) {
            $assignedName = $existingActiveAssignment->responder->user->name ?? 'A specialist';
            return back()->with('error', "{$assignedName} is already actively deployed to this incident. You cannot deploy multiple specialists to the same incident.");
        }

        // 3. Validate assignment inputs
        $data = $request->validate([
            'responder_id'     => ['required', 'exists:responders,id'],
            'dispatcher_notes' => ['nullable', 'string', 'max:1000'],
            'threat_level'     => ['nullable', 'in:Low,Medium,High,Critical'],
        ]);

        // 4. Prevent assigning a responder who is already deployed on ANY active mission
        $isResponderBusy = Assignment::where('responder_id', $data['responder_id'])
            ->whereIn('status', ['assigned', 'accepted'])
            ->whereHas('incident', function ($q) {
                $q->whereIn('status', ['Dispatched', 'Responding']);
            })
            ->exists();

        if ($isResponderBusy) {
            return back()->with('error', 'This specialist is currently deployed on an active emergency mission. Responders cannot take multiple deployments at the same time.');
        }

        // 5. Optionally update threat level if dispatcher re-assessed it
        if (!empty($data['threat_level'])) {
            $incident->update(['threat_level' => $data['threat_level']]);
        }

        // 6. Create the Assignment record
        $assignment = Assignment::create([
            'incident_id'      => $incident->id,
            'responder_id'     => $data['responder_id'],
            'dispatcher_id'    => Auth::id(),
            'status'           => 'assigned',
            'dispatcher_notes' => $data['dispatcher_notes'] ?? null,
            'assigned_at'      => now(),
        ]);

        // 7. Update incident status and set responder unavailable
        $incident->update(['status' => 'Dispatched']);
        $responder = Responder::with('user')->find($data['responder_id']);
        $responder->update(['is_available' => false]);

        // 8. Notify the assigned responder
        Notification::create([
            'user_id' => $responder->user_id,
            'title'   => "New Assignment: [{$incident->threat_level}] {$incident->tracking_code}",
            'message' => "You have been dispatched to {$incident->location_address} for: {$incident->title}",
            'link'    => route('responder.assignments.show', $assignment->id),
        ]);

        // 9. Notify the citizen who reported the incident
        $citizenNotif = Notification::create([
            'user_id' => $incident->citizen_id,
            'title'   => "Responder Dispatched: {$incident->tracking_code}",
            'message' => "{$responder->user->name} ({$responder->specialization}) has been dispatched to your location.",
            'link'    => route('citizen.incidents.show', $incident->id),
        ]);

        try {
            broadcast(new \App\Events\NotificationSent($citizenNotif));
        } catch (\Throwable $e) {}

        // 10. Broadcast Real-Time Incident Status & Assignment update
        try {
            broadcast(new \App\Events\IncidentStatusUpdated($incident));
        } catch (\Throwable $e) {}

        return redirect()->route('dispatcher.incidents.show', $incident->id)
            ->with('success', "Responder {$responder->user->name} successfully dispatched to this emergency!");
    }

    /**
     * Cancel/recall an active assignment so a different specialist can be deployed.
     */
    public function cancelAssignment(Incident $incident, Assignment $assignment)
    {
        // Mark assignment as declined/recalled
        $assignment->update([
            'status'          => 'declined',
            'responder_notes' => 'Deployment recalled by dispatcher.',
            'responded_at'    => now(),
        ]);

        // Free the responder
        if ($assignment->responder) {
            $assignment->responder->update(['is_available' => true]);
        }

        // Return incident back to Pending
        $incident->update(['status' => 'Pending']);

        // Broadcast Real-Time Incident Status update
        try {
            broadcast(new \App\Events\IncidentStatusUpdated($incident));
        } catch (\Throwable $e) {}

        return redirect()->route('dispatcher.incidents.show', $incident->id)
            ->with('info', 'Deployment recalled. The incident is now back in Pending status and you may assign a different available specialist.');
    }

    /**
     * Update threat classification level directly.
     */
    public function updateThreatLevel(Request $request, Incident $incident)
    {
        $data = $request->validate([
            'threat_level' => ['required', 'in:Low,Medium,High,Critical'],
        ]);

        $incident->update(['threat_level' => $data['threat_level']]);

        // Broadcast Real-Time Incident Status update
        try {
            broadcast(new \App\Events\IncidentStatusUpdated($incident));
        } catch (\Throwable $e) {}

        return back()->with('success', "Threat level updated to {$data['threat_level']}.");
    }
}
