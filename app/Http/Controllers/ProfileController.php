<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        $responder = $user->responderProfile;

        return view('profile.edit', compact('user', 'responder'));
    }

    /**
     * Update user profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'           => ['required', 'string', 'max:191'],
            'email'          => ['required', 'email', 'max:191', Rule::unique('users')->ignore($user->id)],
            'phone'          => ['nullable', 'string', 'starts_with:09', 'regex:/^09[0-9\s-]{9,12}$/'],
            'password'       => ['nullable', 'string', 'min:6', 'confirmed'],
            'specialization' => ['nullable', 'string', 'max:191'],
            'bio'            => ['nullable', 'string', 'max:1000'],
        ], [
            'phone.starts_with' => 'The phone number must start with 09 (e.g., 09123456789).',
            'phone.regex'       => 'The phone number must start with 09 and contain 11 digits.',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Update responder profile if user is a responder
        if ($user->isResponder() && $user->responderProfile) {
            $user->responderProfile->update([
                'specialization' => $data['specialization'] ?? $user->responderProfile->specialization,
                'bio'            => $data['bio'] ?? $user->responderProfile->bio,
            ]);
        }

        return back()->with('success', 'Your profile has been updated successfully.');
    }
}
