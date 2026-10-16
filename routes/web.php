<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitizenController;
use App\Http\Controllers\DispatcherController;
use App\Http\Controllers\ResponderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Responder;

/*
|--------------------------------------------------------------------------
| Web Routes - SPECTRESCUE
|--------------------------------------------------------------------------
| All routes are grouped cleanly by role and function for defense clarity.
*/

// ==========================================
// 1. Public & Guest Routes
// ==========================================
Route::get('/', function () {
    $recentIncidents = Incident::with(['incidentType'])->latest()->take(3)->get();
    $stats = [
        'total'     => Incident::count(),
        'resolved'  => Incident::where('status', 'Resolved')->count(),
        'responders'=> Responder::where('verification_status', 'approved')->count(),
    ];
    return view('welcome', compact('recentIncidents', 'stats'));
})->name('home');

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Citizen Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Responder Application
    Route::get('/register-responder', [AuthController::class, 'showResponderRegister'])->name('register.responder');
    Route::post('/register-responder', [AuthController::class, 'registerResponder']);
});

// ==========================================
// 2. Shared Authenticated Routes
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
});

// ==========================================
// 3. Citizen Routes (role: citizen)
// ==========================================
Route::middleware(['auth', 'role:citizen'])->prefix('citizen')->as('citizen.')->group(function () {
    Route::get('/dashboard', [CitizenController::class, 'dashboard'])->name('dashboard');
    Route::get('/incidents', [CitizenController::class, 'dashboard'])->name('incidents.index');
    Route::get('/incidents/create', [CitizenController::class, 'createIncident'])->name('incidents.create');
    Route::post('/incidents', [CitizenController::class, 'storeIncident'])->name('incidents.store');
    Route::get('/incidents/{incident}', [CitizenController::class, 'showIncident'])->name('incidents.show');
    Route::get('/incidents/{incident}/edit', [CitizenController::class, 'editIncident'])->name('incidents.edit');
    Route::put('/incidents/{incident}', [CitizenController::class, 'updateIncident'])->name('incidents.update');
    Route::delete('/incidents/{incident}/evidence/{evidence}', [CitizenController::class, 'destroyEvidence'])->name('incidents.evidence.destroy');
});

// ==========================================
// ==========================================
// 4. Dispatcher Routes (role: dispatcher, admin)
// ==========================================
Route::middleware(['auth', 'role:dispatcher,admin'])->prefix('dispatcher')->as('dispatcher.')->group(function () {
    Route::get('/dashboard', [DispatcherController::class, 'dashboard'])->name('dashboard')->middleware('role:dispatcher');
    Route::get('/incidents/{incident}', [DispatcherController::class, 'showIncident'])->name('incidents.show');
    Route::post('/incidents/{incident}/assign', [DispatcherController::class, 'assignResponder'])->name('incidents.assign');
    Route::post('/incidents/{incident}/threat', [DispatcherController::class, 'updateThreatLevel'])->name('incidents.threat');
    Route::post('/incidents/{incident}/assignments/{assignment}/cancel', [DispatcherController::class, 'cancelAssignment'])->name('incidents.assignments.cancel');
});

// ==========================================
// 5. Responder Routes (role: responder)
// ==========================================
Route::middleware(['auth', 'role:responder'])->prefix('responder')->as('responder.')->group(function () {
    Route::get('/dashboard', [ResponderController::class, 'dashboard'])->name('dashboard');
    Route::get('/assignments/{assignment}', [ResponderController::class, 'showAssignment'])->name('assignments.show');
    Route::post('/assignments/{assignment}/respond', [ResponderController::class, 'respondAssignment'])->name('assignments.respond');
    Route::get('/incidents/{incident}/report', [ResponderController::class, 'createReport'])->name('reports.create');
    Route::post('/incidents/{incident}/report', [ResponderController::class, 'storeReport'])->name('reports.store');
});

// ==========================================
// 6. Admin Routes (role: admin)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/responders', [AdminController::class, 'responders'])->name('responders.index');
    Route::post('/responders/{responder}/verify', [AdminController::class, 'verifyResponder'])->name('responders.verify');
    Route::get('/incidents', [AdminController::class, 'incidents'])->name('incidents.index');
    Route::get('/incidents/{incident}', [DispatcherController::class, 'showIncident'])->name('incidents.show');
    Route::post('/incidents/{incident}/override-status', [AdminController::class, 'overrideIncidentStatus'])->name('incidents.override_status');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle_status');

    // In-App Notifications (broadcast to users)
    Route::get('/notifications',       [AdminController::class, 'notificationsForm'])->name('notifications.index');
    Route::post('/notifications/send', [AdminController::class, 'sendNotification'])->name('notifications.send');

    // Phenomenon Classifications (Incident Types)
    Route::get('/incident-types',                     [AdminController::class, 'incidentTypes'])->name('incident_types.index');
    Route::get('/incident-types/create',              [AdminController::class, 'createIncidentType'])->name('incident_types.create');
    Route::post('/incident-types',                     [AdminController::class, 'storeIncidentType'])->name('incident_types.store');
    Route::get('/incident-types/{incidentType}/edit', [AdminController::class, 'editIncidentType'])->name('incident_types.edit');
    Route::put('/incident-types/{incidentType}',      [AdminController::class, 'updateIncidentType'])->name('incident_types.update');
    Route::delete('/incident-types/{incidentType}',   [AdminController::class, 'destroyIncidentType'])->name('incident_types.destroy');

    // Responder Specializations CRUD
    Route::get('/specializations',                          [AdminController::class, 'specializations'])->name('specializations.index');
    Route::get('/specializations/create',                   [AdminController::class, 'createSpecialization'])->name('specializations.create');
    Route::post('/specializations',                         [AdminController::class, 'storeSpecialization'])->name('specializations.store');
    Route::get('/specializations/{specialization}/edit',    [AdminController::class, 'editSpecialization'])->name('specializations.edit');
    Route::put('/specializations/{specialization}',         [AdminController::class, 'updateSpecialization'])->name('specializations.update');
    Route::delete('/specializations/{specialization}',      [AdminController::class, 'destroySpecialization'])->name('specializations.destroy');

    // System Settings
    Route::get('/settings',  [AdminController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});
