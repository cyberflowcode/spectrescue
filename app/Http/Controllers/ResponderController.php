<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Incident;
use App\Models\Response;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResponderController extends Controller
{
    /**
     * Responder Dashboard: view assigned missions, pending dispatches, and quick stats.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $responder = $user->responderProfile;

        if (!$responder) {
            return redirect()->route('citizen.dashboard')
                ->with('error', 'No responder profile found for this account.');
        }

        // Active missions (Assigned or Accepted)
        $activeAssignments = Assignment::with(['incident.incidentType', 'incident.citizen', 'dispatcher'])
            ->where('responder_id', $responder->id)
            ->whereIn('status', ['assigned', 'accepted'])
            ->whereHas('incident', function ($q) {
                $q->whereIn('status', ['Dispatched', 'Responding']);
            })
            ->latest()
            ->get();

        // Resolved or completed missions
        $completedAssignments = Assignment::with(['incident.incidentType', 'incident.responseReport'])
            ->where('responder_id', $responder->id)
            ->whereHas('incident', function ($q) {
                $q->where('status', 'Resolved');
            })
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'active_missions'    => $activeAssignments->count(),
            'resolved_missions'  => Response::where('responder_id', $responder->id)->count(),
            'is_approved'        => $responder->isApproved(),
        ];

        return view('responder.dashboard', compact('responder', 'activeAssignments', 'completedAssignments', 'stats'));
    }

    /**
     * View details of a specific dispatch assignment.
     */
    public function showAssignment(Assignment $assignment)
    {
        $responder = Auth::user()->responderProfile;

        if (!$responder || $assignment->responder_id !== $responder->id) {
            abort(403, 'You are not authorized to view this emergency mission.');
        }

        $assignment->load(['incident.citizen', 'incident.incidentType', 'incident.evidence', 'dispatcher', 'incident.responseReport']);

        return view('responder.assignments.show', compact('assignment'));
    }

    /**
     * Accept or decline an emergency assignment.
     */
    public function respondAssignment(Request $request, Assignment $assignment)
    {
        $responder = Auth::user()->responderProfile;

        if (!$responder || $assignment->responder_id !== $responder->id) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'action'          => ['required', 'in:accept,decline'],
            'responder_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $incident = $assignment->incident;

        if ($data['action'] === 'accept') {
            $assignment->update([
                'status'          => 'accepted',
                'responder_notes' => $data['responder_notes'] ?? 'Assignment accepted. En route to location.',
                'responded_at'    => now(),
            ]);

            // Update incident to 'Responding'
            $incident->update(['status' => 'Responding']);

            // Notify citizen
            Notification::create([
                'user_id' => $incident->citizen_id,
                'title'   => "Responder En Route: {$incident->tracking_code}",
                'message' => "{$responder->user->name} has accepted your emergency call and is currently responding.",
                'link'    => route('citizen.incidents.show', $incident->id),
            ]);

            // Notify dispatcher
            Notification::create([
                'user_id' => $assignment->dispatcher_id,
                'title'   => "Mission Accepted: {$incident->tracking_code}",
                'message' => "Responder {$responder->user->name} accepted assignment for {$incident->title}.",
                'link'    => route('dispatcher.incidents.show', $incident->id),
            ]);

            return back()->with('success', 'Emergency assignment accepted! Proceed to containment location.');
        } else {
            $assignment->update([
                'status'          => 'declined',
                'responder_notes' => $data['responder_notes'] ?? 'Declined due to unavailability or distance.',
                'responded_at'    => now(),
            ]);

            // Free the responder
            $responder->update(['is_available' => true]);

            // Return incident to Pending so dispatcher can reassign
            $incident->update(['status' => 'Pending']);

            // Notify dispatcher
            Notification::create([
                'user_id' => $assignment->dispatcher_id,
                'title'   => "Assignment Declined: {$incident->tracking_code}",
                'message' => "{$responder->user->name} declined assignment. Incident has been returned to the Pending queue.",
                'link'    => route('dispatcher.incidents.show', $incident->id),
            ]);

            return redirect()->route('responder.dashboard')
                ->with('info', 'You have declined this emergency assignment. The dispatcher has been alerted.');
        }
    }

    /**
     * Show form to submit incident response report.
     */
    public function createReport(Incident $incident)
    {
        $responder = Auth::user()->responderProfile;

        // Verify that this responder is assigned to this incident
        $assignment = Assignment::where('incident_id', $incident->id)
            ->where('responder_id', $responder->id)
            ->firstOrFail();

        // If incident is already resolved or report exists, redirect to briefing
        if ($incident->status === 'Resolved' || $incident->responseReport) {
            return redirect()->route('responder.assignments.show', $assignment->id)
                ->with('info', 'This emergency mission has already been resolved and reported.');
        }

        if ($assignment->status !== 'accepted') {
            return redirect()->route('responder.assignments.show', $assignment->id)
                ->with('error', 'You must accept the emergency mission before filing a containment report.');
        }

        return view('responder.reports.create', compact('incident', 'assignment'));
    }

    /**
     * Store the response and resolution report.
     */
    public function storeReport(Request $request, Incident $incident)
    {
        $responder = Auth::user()->responderProfile;

        // Verify active accepted assignment
        $assignment = Assignment::where('incident_id', $incident->id)
            ->where('responder_id', $responder->id)
            ->where('status', 'accepted')
            ->firstOrFail();

        // 1. Validate response report details
        $data = $request->validate([
            'containment_method' => ['required', 'string', 'max:191'],
            'findings_summary'   => ['required', 'string'],
            'threat_neutralized' => ['required', 'boolean'],
            'evidence_collected' => ['nullable', 'string'],
            'recommendations'    => ['nullable', 'string'],
        ]);

        // 2. Create the Response record
        Response::create([
            'incident_id'        => $incident->id,
            'responder_id'       => $responder->id,
            'containment_method' => $data['containment_method'],
            'findings_summary'   => $data['findings_summary'],
            'threat_neutralized' => $data['threat_neutralized'],
            'evidence_collected' => $data['evidence_collected'] ?? null,
            'recommendations'    => $data['recommendations'] ?? null,
            'submitted_at'       => now(),
        ]);

        // 3. Mark incident as Resolved and assignment as completed
        $incident->update(['status' => 'Resolved']);
        $assignment->update(['status' => 'completed']);
        $responder->update(['is_available' => true]);

        // 4. Notify Citizen
        Notification::create([
            'user_id' => $incident->citizen_id,
            'title'   => "Emergency Resolved: {$incident->tracking_code}",
            'message' => "The paranormal incident at your location has been contained and resolved by {$responder->user->name}.",
            'link'    => route('citizen.incidents.show', $incident->id),
        ]);

        // 5. Notify Dispatcher
        Notification::create([
            'user_id' => $assignment->dispatcher_id,
            'title'   => "Mission Resolved: {$incident->tracking_code}",
            'message' => "{$responder->user->name} has submitted the containment report for {$incident->tracking_code}.",
            'link'    => route('dispatcher.incidents.show', $incident->id),
        ]);

        return redirect()->route('responder.dashboard')
            ->with('success', "Response report submitted! Incident {$incident->tracking_code} is officially marked as Resolved.");
    }
}
