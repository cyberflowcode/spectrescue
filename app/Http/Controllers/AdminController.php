<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Responder;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Response;
use App\Models\Notification;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Admin Dashboard: global system statistics, threat breakdown, and management queues.
     */
    public function dashboard()
    {
        $stats = [
            'total_users'         => User::count(),
            'total_incidents'     => Incident::count(),
            'pending_incidents'   => Incident::where('status', 'Pending')->count(),
            'active_missions'     => Incident::whereIn('status', ['Dispatched', 'Responding'])->count(),
            'resolved_incidents'  => Incident::where('status', 'Resolved')->count(),
            'pending_responders'  => Responder::where('verification_status', 'pending')->count(),
            'approved_responders' => Responder::where('verification_status', 'approved')->count(),
            // Threat level counts
            'critical_threats'    => Incident::where('threat_level', 'Critical')->count(),
            'high_threats'        => Incident::where('threat_level', 'High')->count(),
            'medium_threats'      => Incident::where('threat_level', 'Medium')->count(),
            'low_threats'         => Incident::where('threat_level', 'Low')->count(),
            // User role breakdown
            'citizens'            => User::where('role', 'citizen')->count(),
            'dispatchers'         => User::where('role', 'dispatcher')->count(),
            'responders'          => User::where('role', 'responder')->count(),
            'admins'              => User::where('role', 'admin')->count(),
        ];

        // Recent 5 incidents for activity feed
        $recentIncidents = Incident::with(['citizen', 'incidentType', 'latestAssignment.responder.user'])
            ->latest()
            ->take(5)
            ->get();

        // Responders awaiting verification
        $pendingResponders = Responder::with('user')
            ->where('verification_status', 'pending')
            ->latest()
            ->get();

        return view('admin.dashboard', compact('stats', 'recentIncidents', 'pendingResponders'));
    }

    /**
     * View and filter all registered responders.
     */
    public function responders(Request $request)
    {
        $query = Responder::with(['user', 'responses']);

        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }

        $responders = $query->latest()->paginate(15);

        return view('admin.responders.index', compact('responders'));
    }

    /**
     * Verify (Approve or Reject) a responder application.
     */
    public function verifyResponder(Request $request, Responder $responder)
    {
        $data = $request->validate([
            'verification_status' => ['required', 'in:approved,rejected'],
        ]);

        $responder->update([
            'verification_status' => $data['verification_status'],
            'is_available'        => ($data['verification_status'] === 'approved'),
            'verified_at'         => now(),
        ]);

        $statusLabel = ucfirst($data['verification_status']);

        // Notify the responder of the decision
        Notification::create([
            'user_id' => $responder->user_id,
            'title'   => "Application Status: {$statusLabel}",
            'message' => $data['verification_status'] === 'approved'
                ? "Congratulations! Your responder application as {$responder->specialization} has been approved. You are now eligible for emergency dispatches."
                : "Your responder application has been rejected by administration.",
            'link'    => route('responder.dashboard'),
        ]);

        return back()->with('success', "Responder {$responder->user->name} has been marked as {$statusLabel}.");
    }

    /**
     * Manage and view all incidents across the entire system.
     */
    public function incidents(Request $request)
    {
        $query = Incident::with(['citizen', 'incidentType', 'latestAssignment.responder.user', 'responseReport']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('threat_level')) {
            $query->where('threat_level', $request->threat_level);
        }

        if ($request->filled('type_id')) {
            $query->where('incident_type_id', $request->type_id);
        }

        $incidents = $query->latest()->paginate(15)->withQueryString();
        $types = IncidentType::all();

        return view('admin.incidents.index', compact('incidents', 'types'));
    }

    /**
     * Admin override: change the status of any incident directly.
     */
    public function overrideIncidentStatus(Request $request, Incident $incident)
    {
        $data = $request->validate([
            'status' => ['required', 'in:Pending,Dispatched,Responding,Resolved'],
        ]);

        $incident->update(['status' => $data['status']]);

        return back()->with('success', "Incident {$incident->tracking_code} status updated to {$data['status']}.");
    }

    /**
     * System Reports & Resolution Dossier.
     */
    public function reports(Request $request)
    {
        $query = Response::with(['incident.citizen', 'incident.incidentType', 'responder.user']);

        if ($request->filled('threat_level')) {
            $query->whereHas('incident', function ($q) use ($request) {
                $q->where('threat_level', $request->threat_level);
            });
        }

        $responses = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total_resolved'      => Response::count(),
            'threats_neutralized' => Response::where('threat_neutralized', true)->count(),
            'critical_resolved'   => Response::whereHas('incident', fn($q) => $q->where('threat_level', 'Critical'))->count(),
        ];

        return view('admin.reports.index', compact('responses', 'stats'));
    }

    /**
     * View all registered system users and their roles.
     */
    public function users(Request $request)
    {
        $query = User::with('responderProfile');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user account (Admin Manage Users).
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user account.
     */
    public function storeUser(Request $request)
    {
        $rules = [
            'name'      => ['required', 'string', 'max:191'],
            'email'     => ['required', 'email', 'max:191', 'unique:users,email'],
            'phone'     => ['nullable', 'string', 'starts_with:09', 'regex:/^09[0-9\s-]{9,12}$/'],
            'role'      => ['required', 'in:citizen,dispatcher,responder,admin'],
            'password'  => ['required', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ];

        // Additional fields required if creating a specialist responder
        if ($request->role === 'responder') {
            $rules['specialization']      = ['required', 'string', 'max:191'];
            $rules['badge_number']        = ['required', 'string', 'max:50', 'unique:responders,badge_number'];
            $rules['bio']                 = ['nullable', 'string', 'max:1000'];
            $rules['verification_status'] = ['required', 'in:pending,approved,rejected'];
            $rules['is_available']        = ['required', 'boolean'];
        }

        $data = $request->validate($rules, [
            'phone.starts_with' => 'The phone number must start with 09 (e.g., 09123456789).',
            'phone.regex'       => 'The phone number must start with 09 and contain 11 digits.',
        ]);

        // 1. Create the base user account
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'role'      => $data['role'],
            'password'  => Hash::make($data['password']),
            'is_active' => (bool) $data['is_active'],
        ]);

        // 2. If responder, create linked responder profile
        if ($data['role'] === 'responder') {
            Responder::create([
                'user_id'             => $user->id,
                'specialization'      => $data['specialization'],
                'badge_number'        => $data['badge_number'],
                'bio'                 => $data['bio'] ?? null,
                'verification_status' => $data['verification_status'],
                'is_available'        => (bool) $data['is_available'],
                'verified_at'         => $data['verification_status'] === 'approved' ? now() : null,
            ]);
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "New user {$user->name} ({$user->role}) has been created successfully.");
    }

    /**
     * Toggle a user's active/inactive status (deactivate or reactivate).
     */
    public function toggleUserStatus(User $user)
    {
        // Prevent admin from deactivating their own account
        if ($user->isAdmin()) {
            return back()->with('error', 'Admin accounts cannot be deactivated.');
        }

        // Flip the is_active flag
        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'reactivated' : 'deactivated';

        return back()->with('success', "User {$user->name} has been {$action}.");
    }

    /**
     * View detailed account profile and activity of a specific user.
     */
    public function showUser(User $user)
    {
        $user->load(['responderProfile.assignments.incident', 'responderProfile.responses']);

        $recentIncidents = Incident::where('citizen_id', $user->id)
            ->with('incidentType')
            ->latest()
            ->take(5)
            ->get();

        $recentAssignments = [];
        if ($user->isResponder() && $user->responderProfile) {
            $recentAssignments = $user->responderProfile->assignments()
                ->with(['incident.incidentType', 'dispatcher'])
                ->latest()
                ->take(5)
                ->get();
        }

        $stats = [
            'reported_incidents_count'    => Incident::where('citizen_id', $user->id)->count(),
            'dispatched_missions_count'   => $user->dispatchedAssignments()->count(),
            'responder_assignments_count' => $user->responderProfile ? $user->responderProfile->assignments()->count() : 0,
            'responder_resolved_count'    => $user->responderProfile ? $user->responderProfile->responses()->count() : 0,
        ];

        return view('admin.users.show', compact('user', 'recentIncidents', 'recentAssignments', 'stats'));
    }

    /**
     * Show edit form for a user account.
     */
    public function editUser(User $user)
    {
        $user->load('responderProfile');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user details and optionally their responder profile.
     */
    public function updateUser(Request $request, User $user)
    {
        $rules = [
            'name'      => ['required', 'string', 'max:191'],
            'email'     => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'phone'     => ['nullable', 'string', 'starts_with:09', 'regex:/^09[0-9\s-]{9,12}$/'],
            'role'      => ['required', 'in:citizen,dispatcher,responder,admin'],
            'password'  => ['nullable', 'string', 'min:6'],
            'is_active' => ['required', 'boolean'],
        ];

        if ($request->role === 'responder') {
            $rules['specialization']      = ['required', 'string', 'max:191'];
            $rules['badge_number']        = ['required', 'string', 'max:50'];
            $rules['bio']                 = ['nullable', 'string', 'max:1000'];
            $rules['verification_status'] = ['required', 'in:pending,approved,rejected'];
            $rules['is_available']        = ['required', 'boolean'];
        }

        $data = $request->validate($rules, [
            'phone.starts_with' => 'The phone number must start with 09 (e.g., 09123456789).',
            'phone.regex'       => 'The phone number must start with 09 and contain 11 digits.',
        ]);

        // Update core user attributes
        $user->name      = $data['name'];
        $user->email     = $data['email'];
        $user->phone     = $data['phone'] ?? null;
        $user->role      = $data['role'];
        $user->is_active = (bool) $data['is_active'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // If responder role, update or create responder profile
        if ($data['role'] === 'responder') {
            if ($user->responderProfile) {
                $user->responderProfile->update([
                    'specialization'      => $data['specialization'],
                    'badge_number'        => $data['badge_number'],
                    'bio'                 => $data['bio'] ?? null,
                    'verification_status' => $data['verification_status'],
                    'is_available'        => (bool) $data['is_available'],
                ]);
            } else {
                Responder::create([
                    'user_id'             => $user->id,
                    'specialization'      => $data['specialization'],
                    'badge_number'        => $data['badge_number'],
                    'bio'                 => $data['bio'] ?? null,
                    'verification_status' => $data['verification_status'],
                    'is_available'        => (bool) $data['is_available'],
                ]);
            }
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User account for {$user->name} has been updated successfully.");
    }

    /**
     * Show the Send Notification form and history of past broadcasts.
     */
    public function notificationsForm()
    {
        // Show the 20 most recent notifications sent by admin (title contains [Admin])
        $sentNotifications = Notification::where('title', 'like', '%[Admin]%')
            ->latest()
            ->take(20)
            ->get();

        $userCount = User::count();
        $roles = ['citizen', 'dispatcher', 'responder'];
        $users = User::orderBy('name')->get(['id', 'name', 'role']);

        return view('admin.notifications.index', compact(
            'sentNotifications', 'userCount', 'roles', 'users'
        ));
    }

    /**
     * Send a notification broadcast to all users, a role group, or a specific user.
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:100',
            'message' => 'required|string|max:500',
            'target'  => 'required|in:all,role,user',
            'role'    => 'required_if:target,role|nullable|string',
            'user_id' => 'required_if:target,user|nullable|exists:users,id',
            'link'    => 'nullable|url|max:255',
        ]);

        // Build the list of recipient user IDs
        if ($request->target === 'all') {
            $userIds = User::pluck('id');
        } elseif ($request->target === 'role') {
            $userIds = User::where('role', $request->role)->pluck('id');
        } else {
            $userIds = collect([$request->user_id]);
        }

        // Prefix title with [Admin] so we can filter sent notifications later
        $title = '[Admin] ' . $request->title;

        // Create one notification per recipient
        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'title'   => $title,
                'message' => $request->message,
                'link'    => $request->link ?? null,
                'is_read' => false,
            ]);
        }

        $count = $userIds->count();
        return back()->with('success', "Notification sent to {$count} user(s) successfully.");
    }

    /**
     * Show the System Settings management page.
     */
    public function settings()
    {
        // Load all settings as a key => setting object collection
        $settings = SystemSetting::all()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update system settings from the admin form.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings'   => 'required|array',
            'settings.*' => 'nullable|string|max:500',
        ]);

        foreach ($request->settings as $key => $value) {
            SystemSetting::where('key', $key)->update(['value' => $value ?? '']);
        }

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Display a listing of all Phenomenon Classifications (Incident Types).
     */
    public function incidentTypes()
    {
        $incidentTypes = IncidentType::withCount('incidents')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.incident_types.index', compact('incidentTypes'));
    }

    /**
     * Show the form for creating a new Phenomenon Classification.
     */
    public function createIncidentType()
    {
        return view('admin.incident_types.create');
    }

    /**
     * Store a newly created Phenomenon Classification in storage.
     */
    public function storeIncidentType(Request $request)
    {
        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:191', 'unique:incident_types,name'],
            'default_threat_level' => ['required', 'in:Low,Medium,High,Critical'],
            'description'          => ['nullable', 'string', 'max:1000'],
        ]);

        IncidentType::create($data);

        return redirect()->route('admin.incident_types.index')
            ->with('success', "Phenomenon classification '{$data['name']}' created successfully.");
    }

    /**
     * Show the form for editing an existing Phenomenon Classification.
     */
    public function editIncidentType(IncidentType $incidentType)
    {
        $incidentType->loadCount('incidents');
        return view('admin.incident_types.edit', compact('incidentType'));
    }

    /**
     * Update the specified Phenomenon Classification in storage.
     */
    public function updateIncidentType(Request $request, IncidentType $incidentType)
    {
        $data = $request->validate([
            'name'                 => ['required', 'string', 'max:191', Rule::unique('incident_types', 'name')->ignore($incidentType->id)],
            'default_threat_level' => ['required', 'in:Low,Medium,High,Critical'],
            'description'          => ['nullable', 'string', 'max:1000'],
        ]);

        $incidentType->update($data);

        return redirect()->route('admin.incident_types.index')
            ->with('success', "Phenomenon classification '{$incidentType->name}' updated successfully.");
    }

    /**
     * Remove the specified Phenomenon Classification from storage.
     */
    public function destroyIncidentType(IncidentType $incidentType)
    {
        // Check if there are any incidents linked to this category
        $count = $incidentType->incidents()->count();

        if ($count > 0) {
            return back()->with('error', "Cannot delete '{$incidentType->name}' because {$count} incident(s) are currently categorized under it.");
        }

        $name = $incidentType->name;
        $incidentType->delete();

        return back()->with('success', "Phenomenon classification '{$name}' deleted successfully.");
    }
}
