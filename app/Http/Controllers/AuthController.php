<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Responder;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request)
    {
        // 1. Validate the user input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // 2. Check credentials
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // 3. Block deactivated accounts
        if (!$user->isActive()) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Your account has been deactivated. Please contact the administrator.',
            ]);
        }

        // 4. Single-Device Session Check:
        // Detect if this account already has an active session on another device/browser
        $sessionLifetime = config('session.lifetime', 120);
        $activeThreshold = now()->subMinutes($sessionLifetime)->getTimestamp();
        $currentSessionId = $request->session()->getId();

        $hasActiveSession = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->where('last_activity', '>=', $activeThreshold)
            ->exists();

        // If user chose to terminate other sessions, clear them out
        if ($request->boolean('force_logout')) {
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $currentSessionId)
                ->delete();
            $hasActiveSession = false;
        }

        // If an active session exists on another device, block this new login attempt
        if ($hasActiveSession) {
            return back()->withInput($request->only('email'))->withErrors([
                'session_blocked' => 'This account is currently logged in on another device. Simultaneous logins are disabled for security.',
            ])->with('can_force_logout', true);
        }

        // 5. Log the user in
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Show citizen registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle citizen registration.
     */
    public function register(Request $request)
    {
        // 1. Validate form fields
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:191'],
            'email'    => ['required', 'email', 'max:191', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // 2. Create the user with 'citizen' role
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'role'     => 'citizen',
            'password' => Hash::make($data['password']),
        ]);

        // 3. Log the user in and redirect to citizen dashboard
        Auth::login($user);

        return redirect()->route('citizen.dashboard')
            ->with('success', 'Your Citizen account has been created successfully!');
    }

    /**
     * Show responder (exorcist/ghost hunter) registration form.
     */
    public function showResponderRegister()
    {
        return view('auth.register-responder');
    }

    /**
     * Handle responder registration.
     */
    public function registerResponder(Request $request)
    {
        // 1. Validate both user and responder profile information
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:191'],
            'email'          => ['required', 'email', 'max:191', 'unique:users,email'],
            'phone'          => ['required', 'string', 'max:50'],
            'password'       => ['required', 'string', 'min:6', 'confirmed'],
            'specialization' => ['required', 'string', 'max:191'],
            'badge_number'   => ['required', 'string', 'max:100', 'unique:responders,badge_number'],
            'bio'            => ['nullable', 'string', 'max:1000'],
        ]);

        // 2. Create the User account with 'responder' role
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'role'     => 'responder',
            'password' => Hash::make($data['password']),
        ]);

        // 3. Create the Responder profile (Pending approval by Admin)
        Responder::create([
            'user_id'             => $user->id,
            'specialization'      => $data['specialization'],
            'badge_number'        => $data['badge_number'],
            'bio'                 => $data['bio'] ?? null,
            'verification_status' => 'pending',
            'is_available'        => false,
        ]);

        // 4. Notify Administrators about the new applicant
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'title'   => 'New Responder Registration',
                'message' => "{$user->name} has applied as {$data['specialization']}. Verification required.",
                'link'    => route('admin.responders.index'),
            ]);
        }

        // 5. Log in the newly registered responder
        Auth::login($user);

        return redirect()->route('responder.dashboard')
            ->with('success', 'Your responder application has been submitted and is pending Administrator verification.');
    }

    /**
     * Log out the current authenticated user.
     */
    public function logout(Request $request)
    {
        $sessionId = $request->session()->getId();

        Auth::logout();

        // Remove the session from database to immediately release the device lock
        if ($sessionId) {
            DB::table('sessions')->where('id', $sessionId)->delete();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Helper to redirect users to their appropriate dashboard by role.
     */
    private function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'dispatcher' => redirect()->route('dispatcher.dashboard'),
            'responder'  => redirect()->route('responder.dashboard'),
            default      => redirect()->route('citizen.dashboard'),
        };
    }
}
