@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-extrabold text-white">Profile Settings</h1>
        <p class="text-xs text-slate-400 mt-0.5">Update your contact info, specialization, and password</p>
    </div>

    {{-- Account Info Card (readonly display) --}}
    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex items-center space-x-4">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-lg font-extrabold shrink-0
            @if($user->isAdmin()) bg-indigo-500/20 text-indigo-300 border border-indigo-500/30
            @elseif($user->isDispatcher()) bg-amber-500/20 text-amber-300 border border-amber-500/30
            @elseif($user->isResponder()) bg-cyan-500/20 text-cyan-300 border border-cyan-500/30
            @else bg-slate-700/50 text-slate-300 border border-slate-600/40 @endif">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div>
            <div class="font-bold text-white text-base">{{ $user->name }}</div>
            <div class="text-xs text-slate-400">{{ $user->email }}</div>
            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase
                @if($user->isAdmin()) bg-indigo-500/20 text-indigo-300 border border-indigo-500/30
                @elseif($user->isDispatcher()) bg-amber-500/20 text-amber-300 border border-amber-500/30
                @elseif($user->isResponder()) bg-cyan-500/20 text-cyan-300 border border-cyan-500/30
                @else bg-slate-700/50 text-slate-300 border border-slate-600/40 @endif">
                {{ $user->role }}
            </span>
        </div>
    </div>

    {{-- Edit Form --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-xl space-y-5">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name + Phone row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-semibold text-slate-300">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm
                        focus:outline-none transition-colors
                        {{ $errors->has('name') ? 'border-red-500' : 'border-slate-700 focus:border-cyan-500' }}">
                    @error('name')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="phone" class="block text-xs font-semibold text-slate-300">
                        Phone <span class="text-slate-500 font-normal">(starts with 09)</span>
                    </label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                        placeholder="0912 345 6789" maxlength="13" inputmode="numeric"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm font-mono
                        focus:outline-none transition-colors
                        {{ $errors->has('phone') ? 'border-red-500' : 'border-slate-700 focus:border-cyan-500' }}">
                    @error('phone')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-300">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm
                    focus:outline-none transition-colors
                    {{ $errors->has('email') ? 'border-red-500' : 'border-slate-700 focus:border-cyan-500' }}">
                @error('email')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            {{-- Responder-only credentials --}}
            @if($user->isResponder() && $responder)
                <div class="pt-4 border-t border-slate-800 space-y-4">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        <span class="text-xs font-mono uppercase text-cyan-400 font-bold">Responder Credentials</span>
                    </div>
                    <div class="space-y-1.5">
                        <label for="specialization" class="block text-xs font-semibold text-slate-300">Field Specialization</label>
                        <input id="specialization" type="text" name="specialization"
                            value="{{ old('specialization', $responder->specialization) }}"
                            placeholder="e.g. Exorcist, EMF Analyst, Demonologist"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-500 transition-colors">
                        @error('specialization')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="bio" class="block text-xs font-semibold text-slate-300">Bio & Experience Statement</label>
                        <textarea id="bio" name="bio" rows="3"
                            placeholder="Describe your paranormal investigation experience and certifications..."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-500 transition-colors resize-none">{{ old('bio', $responder->bio) }}</textarea>
                        @error('bio')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>
            @endif

            {{-- Change Password --}}
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                    <span class="text-xs font-mono uppercase text-slate-400">Change Password <span class="text-slate-600 normal-case font-normal">(leave blank to keep current)</span></span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">New Password</label>
                        <input id="password" type="password" name="password" placeholder="Min. 6 characters"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-500 transition-colors">
                        @error('password')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300">Confirm</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repeat password"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full py-2.5 rounded-xl font-bold text-sm bg-cyan-500 hover:bg-cyan-400 text-slate-950
                shadow-lg shadow-cyan-500/20 transition-all hover:-translate-y-0.5 transform">
                Save Changes →
            </button>
        </form>
    </div>

</div>
@endsection
