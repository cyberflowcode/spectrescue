@extends('layouts.app')

@section('title', 'Add New User')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Top Back Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
            <span>&larr; Back to User Registry</span>
        </a>
        <span class="text-xs font-mono text-slate-400">Manage Users &bull; <strong class="text-indigo-400">Create Account</strong></span>
    </div>

    {{-- Create Form Card --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
        <div class="border-b border-slate-800 pb-4">
            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">User Creation</span>
            <h1 class="text-2xl font-bold text-white mt-1">Add New User Account</h1>
            <p class="text-xs text-slate-400 mt-1">Register a citizen caller, field responder, dispatch officer, or system administrator.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6" id="createUserForm">
            @csrf

            {{-- SECTION 1: Core Account Credentials --}}
            <div class="space-y-4">
                <h2 class="text-xs font-mono uppercase tracking-wider text-cyan-400 font-bold">1. Account Credentials &amp; Role</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Full Name --}}
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Full Name <span class="text-red-400">*</span>
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            placeholder="e.g. Johnathan Constantine"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm">
                        @error('name')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Address --}}
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Email Address <span class="text-red-400">*</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            placeholder="agent@spectrescue.org"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm font-mono">
                        @error('email')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Phone Number --}}
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Contact Phone <span class="text-slate-500 font-normal lowercase">(starts with 09)</span>
                        </label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="0912 345 6789" maxlength="13"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border text-white placeholder-slate-500 focus:outline-none focus:ring-1 text-sm font-mono
                            {{ $errors->has('phone') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-slate-700 focus:border-indigo-500 focus:ring-indigo-500' }}">
                        @error('phone')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Account Active Status --}}
                    <div class="space-y-1.5">
                        <label for="is_active" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Account Status <span class="text-red-400">*</span>
                        </label>
                        <select id="is_active" name="is_active" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm font-mono">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>● Active (Normal Access)</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>● Deactivated (Access Suspended)</option>
                        </select>
                        @error('is_active')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- System Role --}}
                    <div class="space-y-1.5">
                        <label for="role" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            System Role <span class="text-red-400">*</span>
                        </label>
                        <select id="role" name="role" required onchange="toggleResponderSection(this.value)"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm font-mono">
                            <option value="citizen" {{ old('role', 'citizen') === 'citizen' ? 'selected' : '' }}>Citizen (Reports Emergencies)</option>
                            <option value="responder" {{ old('role') === 'responder' ? 'selected' : '' }}>Responder (Field Specialist)</option>
                            <option value="dispatcher" {{ old('role') === 'dispatcher' ? 'selected' : '' }}>Dispatcher (Duty Officer)</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (HQ Administrator)</option>
                        </select>
                        @error('role')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Initial Password --}}
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Account Password <span class="text-red-400">*</span>
                        </label>
                        <input id="password" type="password" name="password" required minlength="6"
                            placeholder="Min. 6 characters"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm font-mono">
                        @error('password')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Responder Profile (shown only when role === 'responder') --}}
            <div id="responderSection" class="{{ old('role') === 'responder' ? '' : 'hidden' }} space-y-4 pt-4 border-t border-slate-800">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <h2 class="text-xs font-mono uppercase tracking-wider text-cyan-400 font-bold">2. Specialist Responder Credentials</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Specialization --}}
                    <div class="space-y-1.5">
                        <label for="specialization" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Primary Specialization <span class="text-red-400">*</span>
                        </label>
                        <select id="specialization" name="specialization" onchange="updateBadgePrefix(this)"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                            <option value="">-- Select Field Specialization --</option>
                            @foreach($specializations ?? [] as $spec)
                                <option value="{{ $spec->name }}" data-prefix="{{ $spec->code_prefix }}"
                                    {{ old('specialization') === $spec->name ? 'selected' : '' }}>
                                    {{ $spec->name }} ({{ $spec->code_prefix }})
                                </option>
                            @endforeach
                        </select>
                        @error('specialization')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Badge Number --}}
                    <div class="space-y-1.5">
                        <label for="badge_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Badge / ID Number <span class="text-red-400">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <input id="badge_number" type="text" name="badge_number" value="{{ old('badge_number') }}"
                                placeholder="RES-2026-XXXX"
                                class="w-full pl-4 pr-24 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm font-mono">
                            <button type="button" onclick="generateBadgeNumber()"
                                class="absolute right-1.5 px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold shadow-md shadow-cyan-600/20 transition-all flex items-center space-x-1 cursor-pointer"
                                title="Auto-generate badge number">
                                <svg class="w-3.5 h-3.5 text-cyan-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>Auto</span>
                            </button>
                        </div>
                        @error('badge_number')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Verification Status --}}
                    <div class="space-y-1.5">
                        <label for="verification_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Accreditation Status <span class="text-red-400">*</span>
                        </label>
                        <select id="verification_status" name="verification_status"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm font-mono">
                            <option value="approved" {{ old('verification_status', 'approved') === 'approved' ? 'selected' : '' }}>Approved (Ready for Dispatch)</option>
                            <option value="pending" {{ old('verification_status') === 'pending' ? 'selected' : '' }}>Pending (Under Review)</option>
                            <option value="rejected" {{ old('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected (Application Denied)</option>
                        </select>
                        @error('verification_status')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deployment Availability --}}
                    <div class="space-y-1.5">
                        <label for="is_available" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Deployment Availability <span class="text-red-400">*</span>
                        </label>
                        <select id="is_available" name="is_available"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm font-mono">
                            <option value="1" {{ old('is_available', '1') == '1' ? 'selected' : '' }}>● Available for Field Missions</option>
                            <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>● Unavailable / Off Duty</option>
                        </select>
                        @error('is_available')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Bio Statement --}}
                <div class="space-y-1.5">
                    <label for="bio" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Specialist Statement / Bio (Optional)
                    </label>
                    <textarea id="bio" name="bio" rows="3"
                        placeholder="Brief summary of experience, equipment, or background in paranormal fields..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">{{ old('bio') }}</textarea>
                    @error('bio')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Submit Buttons --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                <a href="{{ route('admin.users.index') }}"
                    class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                    ✓ Create User Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.toggleResponderSection = function(role) {
        var section = document.getElementById('responderSection');
        var specInput = document.getElementById('specialization');
        var badgeInput = document.getElementById('badge_number');
        if (!section) return;

        if (role === 'responder') {
            section.classList.remove('hidden');
            if (specInput) specInput.required = true;
            if (badgeInput) {
                badgeInput.required = true;
                if (!badgeInput.value) {
                    window.generateBadgeNumber();
                }
            }
        } else {
            section.classList.add('hidden');
            if (specInput) specInput.required = false;
            if (badgeInput) badgeInput.required = false;
        }
    };

    window.updateBadgePrefix = function(selectEl) {
        window.generateBadgeNumber();
    };

    window.generateBadgeNumber = function() {
        var specSelect = document.getElementById('specialization');
        var badgeInput = document.getElementById('badge_number');
        if (!badgeInput) return;
        var prefix = 'RSP';
        if (specSelect && specSelect.selectedIndex >= 0) {
            var opt = specSelect.options[specSelect.selectedIndex];
            if (opt && opt.dataset.prefix) {
                prefix = opt.dataset.prefix;
            }
        }
        var randomNum = Math.floor(1000 + Math.random() * 9000);
        badgeInput.value = prefix + '-' + randomNum;
    };

    (function() {
        function initCreateUserForm() {
            var roleSelect = document.getElementById('role');
            if (roleSelect) {
                window.toggleResponderSection(roleSelect.value);
            }

            var phoneEl = document.getElementById('phone');
            if (phoneEl && !phoneEl.dataset.formatBound) {
                phoneEl.dataset.formatBound = 'true';
                phoneEl.addEventListener('input', function(e) {
                    var v = e.target.value.replace(/\D/g, '').substring(0, 11);
                    var formatted = '';
                    if (v.length > 0) formatted += v.substring(0, 4);
                    if (v.length > 4) formatted += ' ' + v.substring(4, 7);
                    if (v.length > 7) formatted += ' ' + v.substring(7, 11);
                    e.target.value = formatted;
                });
            }
        }

        initCreateUserForm();
        document.addEventListener('DOMContentLoaded', initCreateUserForm);
        document.addEventListener('spa:navigated', initCreateUserForm);
    })();
</script>
@endsection
