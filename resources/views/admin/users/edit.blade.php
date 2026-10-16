@extends('layouts.app')

@section('title', 'Edit User: ' . $user->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Top Back Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.show', $user->id) }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
            <span>&larr; Back to User Dossier</span>
        </a>
        <span class="text-xs font-mono text-slate-400">User ID: <strong class="text-white">#{{ $user->id }}</strong></span>
    </div>

    {{-- Edit Form Card --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
        <div class="border-b border-slate-800 pb-4">
            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">User Modification</span>
            <h1 class="text-2xl font-bold text-white mt-1">Edit User Profile</h1>
            <p class="text-xs text-slate-400 mt-1">Modify account credentials, role permissions, and specialist accreditation.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- SECTION 1: Core Credentials --}}
            <div class="space-y-4">
                <h2 class="text-xs font-mono uppercase tracking-wider text-cyan-400 font-bold">1. Core Account Credentials</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Full Name --}}
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Full Name <span class="text-red-400">*</span>
                        </label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                        @error('name')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Address --}}
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Email Address <span class="text-red-400">*</span>
                        </label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm font-mono">
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
                        <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0912 345 6789" maxlength="13"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border text-white placeholder-slate-500 focus:outline-none focus:ring-1 text-sm font-mono
                            {{ $errors->has('phone') ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-slate-700 focus:border-amber-500 focus:ring-amber-500' }}">
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
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm font-mono">
                            <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>● Active (Normal Access)</option>
                            <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>● Deactivated (Access Suspended)</option>
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
                        <select id="role" name="role" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm font-mono">
                            <option value="citizen" {{ old('role', $user->role) === 'citizen' ? 'selected' : '' }}>Citizen (Reports Emergencies)</option>
                            <option value="responder" {{ old('role', $user->role) === 'responder' ? 'selected' : '' }}>Responder (Field Specialist)</option>
                            <option value="dispatcher" {{ old('role', $user->role) === 'dispatcher' ? 'selected' : '' }}>Dispatcher (Duty Officer)</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (HQ Administrator)</option>
                        </select>
                        @error('role')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Reset (Optional) --}}
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Reset Password <span class="text-slate-500 font-normal lowercase">(optional)</span>
                        </label>
                        <input id="password" type="password" name="password" placeholder="Leave blank to keep current"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                        <span class="block text-[11px] text-slate-500">Only fill this if you wish to change or reset their password.</span>
                        @error('password')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- SECTION 2: Responder Accreditation (Shown when role is Responder) --}}
            <div id="responder-section" class="space-y-4 pt-4 border-t border-slate-800 {{ old('role', $user->role) === 'responder' ? '' : 'hidden' }}">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <h2 class="text-xs font-mono uppercase tracking-wider text-cyan-400 font-bold">2. Specialist Accreditation Dossier</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Specialization --}}
                    <div class="space-y-1.5">
                        <label for="specialization" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Specialization <span class="text-red-400">*</span>
                        </label>
                        <select id="specialization" name="specialization"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                            <option value="">-- Select Field Specialization --</option>
                            @php
                                $currentSpec = old('specialization', $user->responderProfile->specialization ?? '');
                                $foundMatch = false;
                            @endphp
                            @foreach($specializations ?? [] as $spec)
                                @php
                                    $isSelected = ($currentSpec === $spec->name);
                                    if ($isSelected) $foundMatch = true;
                                @endphp
                                <option value="{{ $spec->name }}" data-prefix="{{ $spec->code_prefix }}"
                                    {{ $isSelected ? 'selected' : '' }}>
                                    {{ $spec->name }} ({{ $spec->code_prefix }})
                                </option>
                            @endforeach
                            @if($currentSpec && !$foundMatch)
                                <option value="{{ $currentSpec }}" selected>{{ $currentSpec }} (Custom)</option>
                            @endif
                        </select>
                        @error('specialization')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Badge Number --}}
                    <div class="space-y-1.5">
                        <label for="badge_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Badge Number <span class="text-red-400">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <input id="badge_number" type="text" name="badge_number"
                                value="{{ old('badge_number', $user->responderProfile->badge_number ?? '') }}"
                                placeholder="e.g. EXO-4921"
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
                            Verification Accreditation <span class="text-red-400">*</span>
                        </label>
                        <select id="verification_status" name="verification_status"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm font-mono">
                            <option value="approved" {{ old('verification_status', $user->responderProfile->verification_status ?? '') === 'approved' ? 'selected' : '' }}>Approved (Authorized to Respond)</option>
                            <option value="pending" {{ old('verification_status', $user->responderProfile->verification_status ?? '') === 'pending' ? 'selected' : '' }}>Pending Review</option>
                            <option value="rejected" {{ old('verification_status', $user->responderProfile->verification_status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected (Disqualified)</option>
                        </select>
                        @error('verification_status')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Availability Status --}}
                    <div class="space-y-1.5">
                        <label for="is_available" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Availability for Dispatch <span class="text-red-400">*</span>
                        </label>
                        <select id="is_available" name="is_available"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm font-mono">
                            <option value="1" {{ old('is_available', $user->responderProfile->is_available ?? 1) ? 'selected' : '' }}>● Available (Ready for Dispatches)</option>
                            <option value="0" {{ !old('is_available', $user->responderProfile->is_available ?? 1) ? 'selected' : '' }}>● Busy / Deployed / Off-Duty</option>
                        </select>
                        @error('is_available')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Bio --}}
                <div class="space-y-1.5">
                    <label for="bio" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Operational Bio &amp; Equipment Directives
                    </label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Brief background, certifications, and supernatural equipment..."
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">{{ old('bio', $user->responderProfile->bio ?? '') }}</textarea>
                    @error('bio')
                        <p class="text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                <a href="{{ route('admin.users.show', $user->id) }}"
                    class="px-5 py-2.5 rounded-xl text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-500/25 transition-all flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save User Changes</span>
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle responder section dynamically when role selection changes
        const roleSelect = document.getElementById('role');
        const responderSection = document.getElementById('responder-section');

        if (roleSelect && responderSection) {
            roleSelect.addEventListener('change', function() {
                if (this.value === 'responder') {
                    responderSection.classList.remove('hidden');
                } else {
                    responderSection.classList.add('hidden');
                }
            });
        }

        // Auto format phone number (09XX XXX XXXX)
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let val = this.value.replace(/\D/g, '');
                if (val.length > 11) val = val.substring(0, 11);

                let formatted = '';
                if (val.length > 0) formatted += val.substring(0, 4);
                if (val.length >= 5) formatted += ' ' + val.substring(4, 7);
                if (val.length >= 8) formatted += ' ' + val.substring(7, 11);

                this.value = formatted;
            });
        }
    });

    function generateBadgeNumber() {
        const specSelect = document.getElementById('specialization');
        let prefix = 'RSP';
        if (specSelect && specSelect.selectedIndex >= 0) {
            const opt = specSelect.options[specSelect.selectedIndex];
            if (opt && opt.dataset.prefix) {
                prefix = opt.dataset.prefix;
            }
        }
        const randomNum = Math.floor(1000 + Math.random() * 9000);
        document.getElementById('badge_number').value = prefix + '-' + randomNum;
    }
</script>
@endpush
@endsection
