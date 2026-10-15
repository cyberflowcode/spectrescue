<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Evidence;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CitizenController extends Controller
{
    /**
     * Display Citizen Dashboard with summary stats and recent incidents.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // 1. Fetch all incidents reported by this citizen
        $incidents = Incident::with(['incidentType', 'latestAssignment.responder.user', 'responseReport'])
            ->where('citizen_id', $user->id)
            ->latest()
            ->paginate(10);

        // 2. Calculate simple stats for quick cards
        $stats = [
            'total'      => Incident::where('citizen_id', $user->id)->count(),
            'pending'    => Incident::where('citizen_id', $user->id)->where('status', 'Pending')->count(),
            'dispatched' => Incident::where('citizen_id', $user->id)->whereIn('status', ['Dispatched', 'Responding'])->count(),
            'resolved'   => Incident::where('citizen_id', $user->id)->where('status', 'Resolved')->count(),
        ];

        return view('citizen.dashboard', compact('incidents', 'stats'));
    }

    /**
     * Show the paranormal incident reporting form.
     */
    public function createIncident()
    {
        $incidentTypes = IncidentType::all();

        return view('citizen.incidents.create', compact('incidentTypes'));
    }

    /**
     * Store a newly reported emergency incident.
     */
    public function storeIncident(Request $request)
    {
        // 1. Validate incident inputs and optional evidence photo
        $data = $request->validate([
            'incident_type_id' => ['required', 'exists:incident_types,id'],
            'title'            => ['required', 'string', 'max:191'],
            'description'      => ['required', 'string'],
            'location_address' => ['required', 'string', 'max:191'],
            'location_city'    => ['required', 'string', 'max:100'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'threat_level'     => ['required', 'in:Low,Medium,High,Critical'],
            'evidence_file'    => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:10240'],
            'evidence_caption' => ['nullable', 'string', 'max:191'],
        ]);

        // 2. Generate unique tracking code (e.g. PAR-2026-0004)
        $nextId = (Incident::max('id') ?? 0) + 1;
        $trackingCode = 'PAR-' . date('Y') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // 3. Create the Incident record
        $incident = Incident::create([
            'citizen_id'       => Auth::id(),
            'incident_type_id' => $data['incident_type_id'],
            'tracking_code'    => $trackingCode,
            'title'            => $data['title'],
            'description'      => $data['description'],
            'location_address' => $data['location_address'],
            'location_city'    => $data['location_city'],
            'latitude'         => $data['latitude'] ?? null,
            'longitude'        => $data['longitude'] ?? null,
            'threat_level'     => $data['threat_level'],
            'status'           => 'Pending',
        ]);

        // 4. Handle evidence upload if provided
        if ($request->hasFile('evidence_file')) {
            $path = $request->file('evidence_file')->store('evidence', 'public');

            Evidence::create([
                'incident_id' => $incident->id,
                'file_path'   => $path,
                'file_name'   => $request->file('evidence_file')->getClientOriginalName(),
                'file_type'   => str_starts_with($request->file('evidence_file')->getMimeType(), 'image') ? 'image' : 'document',
                'caption'     => $data['evidence_caption'] ?? 'Initial citizen upload',
            ]);
        }

        // 5. Notify all active dispatchers
        $dispatchers = User::where('role', 'dispatcher')->get();
        foreach ($dispatchers as $dispatcher) {
            Notification::create([
                'user_id' => $dispatcher->id,
                'title'   => "New Emergency: [{$incident->threat_level}] {$incident->tracking_code}",
                'message' => "Incident reported at {$incident->location_address}: {$incident->title}",
                'link'    => route('dispatcher.incidents.show', $incident->id),
            ]);
        }

        return redirect()->route('citizen.incidents.show', $incident->id)
            ->with('success', "Emergency report submitted successfully! Tracking Code: {$incident->tracking_code}");
    }

    /**
     * Show tracking and details of a specific incident.
     */
    public function showIncident(Incident $incident)
    {
        // Ensure only the owner (or staff) can view
        if ($incident->citizen_id !== Auth::id() && !in_array(Auth::user()->role, ['admin', 'dispatcher', 'responder'])) {
            abort(403, 'You are not authorized to view this emergency incident.');
        }

        $incident->load(['incidentType', 'evidence', 'assignments.responder.user', 'responseReport.responder.user']);

        return view('citizen.incidents.show', compact('incident'));
    }

    /**
     * Show the edit form for an existing emergency incident report.
     */
    public function editIncident(Incident $incident)
    {
        // Ensure only the citizen who filed it (or admin) can edit
        if ($incident->citizen_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to edit this incident report.');
        }

        // Prevent editing if the incident has already been resolved and closed
        if ($incident->status === 'Resolved') {
            return redirect()->route('citizen.incidents.show', $incident->id)
                ->with('error', 'This emergency incident has already been resolved and archived. It can no longer be edited.');
        }

        $incidentTypes = IncidentType::all();
        $incident->load('evidence');

        return view('citizen.incidents.edit', compact('incident', 'incidentTypes'));
    }

    /**
     * Update an existing emergency incident report and ground zero coordinates.
     */
    public function updateIncident(Request $request, Incident $incident)
    {
        // Ensure only the citizen who filed it (or admin) can update
        if ($incident->citizen_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to update this incident report.');
        }

        if ($incident->status === 'Resolved') {
            return redirect()->route('citizen.incidents.show', $incident->id)
                ->with('error', 'Resolved incident reports cannot be modified.');
        }

        // 1. Validate updated inputs
        $data = $request->validate([
            'incident_type_id' => ['required', 'exists:incident_types,id'],
            'title'            => ['required', 'string', 'max:191'],
            'description'      => ['required', 'string'],
            'location_address' => ['required', 'string', 'max:191'],
            'location_city'    => ['required', 'string', 'max:100'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
            'threat_level'     => ['required', 'in:Low,Medium,High,Critical'],
            'evidence_file'    => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,pdf', 'max:10240'],
            'evidence_caption' => ['nullable', 'string', 'max:191'],
        ]);

        // 2. Update the Incident record
        $incident->update([
            'incident_type_id' => $data['incident_type_id'],
            'title'            => $data['title'],
            'description'      => $data['description'],
            'location_address' => $data['location_address'],
            'location_city'    => $data['location_city'],
            'latitude'         => $data['latitude'] ?? null,
            'longitude'        => $data['longitude'] ?? null,
            'threat_level'     => $data['threat_level'],
        ]);

        // 3. Handle additional evidence upload if attached
        if ($request->hasFile('evidence_file')) {
            $path = $request->file('evidence_file')->store('evidence', 'public');

            Evidence::create([
                'incident_id' => $incident->id,
                'file_path'   => $path,
                'file_name'   => $request->file('evidence_file')->getClientOriginalName(),
                'file_type'   => str_starts_with($request->file('evidence_file')->getMimeType(), 'image') ? 'image' : 'document',
                'caption'     => $data['evidence_caption'] ?? 'Updated citizen upload',
            ]);
        }

        return redirect()->route('citizen.incidents.show', $incident->id)
            ->with('success', "Emergency report {$incident->tracking_code} updated successfully! Ground zero coordinates and details saved.");
    }

    /**
     * Remove an attached evidence file from an incident report.
     */
    public function destroyEvidence(Incident $incident, Evidence $evidence)
    {
        // 1. Ensure the evidence belongs to this incident
        if ($evidence->incident_id !== $incident->id) {
            abort(404, 'Evidence not found for this incident.');
        }

        // 2. Ensure only the citizen who owns the incident (or admin) can remove evidence
        if ($incident->citizen_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to delete evidence from this incident.');
        }

        // 3. Do not allow deletion if the incident has already been resolved
        if ($incident->status === 'Resolved') {
            return redirect()->route('citizen.incidents.show', $incident->id)
                ->with('error', 'Resolved incident reports cannot be modified.');
        }

        // 4. Delete the physical file from storage if it exists
        if ($evidence->file_path && Storage::disk('public')->exists($evidence->file_path)) {
            Storage::disk('public')->delete($evidence->file_path);
        }

        // 5. Delete the database record
        $evidence->delete();

        return back()->with('success', 'Evidence image removed successfully.');
    }
}
