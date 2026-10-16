@extends('layouts.app')

@section('title', 'Responder Application')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-start justify-center py-10 px-4">
    <div class="w-full max-w-lg space-y-6">

        {{-- Header --}}
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center mx-auto shadow-xl shadow-indigo-600/25">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-white">Responder Application</h1>
                <p class="text-xs text-slate-400 mt-0.5">Apply to join the SPECTRESCUE field specialist network</p>
            </div>
        </div>

        {{-- Notice Banner --}}
        <div class="p-4 rounded-xl bg-indigo-950/30 border border-indigo-500/30 flex items-start space-x-3 text-xs text-indigo-200">
            <svg class="w-4 h-4 text-indigo-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Applications are reviewed by administrators before you can receive emergency dispatch assignments. You'll be notified of the decision.</span>
        </div>

        {{-- Form Card --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-7 shadow-2xl shadow-slate-950/60 space-y-5">
            <form method="POST" action="{{ route('register.responder') }}" class="space-y-4">
                @csrf

                {{-- Personal Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-semibold text-slate-300">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            required autofocus placeholder="Fr. John Ward"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                            focus:outline-none transition-colors
                            {{ $errors->has('name') ? 'border-red-500' : 'border-slate-700 focus:border-indigo-500' }}">
                        @error('name')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-slate-300">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            required placeholder="you@example.com"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                            focus:outline-none transition-colors
                            {{ $errors->has('email') ? 'border-red-500' : 'border-slate-700 focus:border-indigo-500' }}">
                        @error('email')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-semibold text-slate-300">
                            Emergency Phone <span class="text-slate-400 font-normal">(starts with 09)</span> <span class="text-red-400">*</span>
                        </label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                            required placeholder="0912 345 6789" maxlength="13" inputmode="numeric"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                            focus:outline-none transition-colors font-mono
                            {{ $errors->has('phone') ? 'border-red-500' : 'border-slate-700 focus:border-indigo-500' }}">
                        @error('phone')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="badge_number" class="block text-xs font-semibold text-slate-300">
                                Badge / License ID <span class="text-red-400">*</span>
                            </label>
                            <button type="button" id="btn-auto-badge" onclick="generateAutoBadge()"
                                class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-600/30 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/40 transition-all cursor-pointer shadow-sm">
                                <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>⚡ Auto Badge</span>
                            </button>
                        </div>
                        <input id="badge_number" type="text" name="badge_number" value="{{ old('badge_number') }}"
                            required placeholder="Click '⚡ Auto Badge'"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950/80 border text-white text-sm placeholder-slate-600
                            focus:outline-none transition-colors font-mono
                            {{ $errors->has('badge_number') || $errors->has('badge_auto_generated') ? 'border-red-500' : 'border-slate-700 focus:border-indigo-500' }}">
                        <input type="hidden" id="badge_auto_generated" name="badge_auto_generated" value="{{ old('badge_auto_generated', '0') }}">
                        @error('badge_number')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                        @error('badge_auto_generated')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
                        <p class="text-[11px] text-slate-500">Badge must be auto-generated by the system. Click <strong>⚡ Auto Badge</strong> above.</p>
                    </div>
                </div>

                {{-- Specialization --}}
                <div class="space-y-1.5">
                    <label for="specialization" class="block text-xs font-semibold text-slate-300">
                        Field Specialization <span class="text-red-400">*</span>
                    </label>
                    <select id="specialization" name="specialization" required onchange="handleSpecializationChange()"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                        focus:outline-none focus:border-indigo-500 transition-colors">
                        <option value="" data-prefix="RSP">Select your specialization…</option>
                        @if(isset($specializations) && $specializations->count() > 0)
                            @foreach($specializations as $spec)
                                <option value="{{ $spec->name }}"
                                    data-prefix="{{ $spec->code_prefix ?? 'RSP' }}"
                                    {{ old('specialization') == $spec->name ? 'selected' : '' }}>
                                    {{ $spec->name }} ({{ $spec->code_prefix ?? 'RSP' }})
                                </option>
                            @endforeach
                        @else
                            @foreach([
                                ['name' => 'Senior Exorcist & Demonologist', 'prefix' => 'EXO'],
                                ['name' => 'High-Tech Ghost Hunter (EMF/Thermal)', 'prefix' => 'GHT'],
                                ['name' => 'Cleric / Sanctification Specialist', 'prefix' => 'CLR'],
                                ['name' => 'Clairvoyant Medium & Astral Communicator', 'prefix' => 'MED'],
                                ['name' => 'Cryptid & Anomaly Tracker', 'prefix' => 'CRY'],
                                ['name' => 'Occult Relic Containment Specialist', 'prefix' => 'OCC'],
                            ] as $defaultSpec)
                                <option value="{{ $defaultSpec['name'] }}"
                                    data-prefix="{{ $defaultSpec['prefix'] }}"
                                    {{ old('specialization') == $defaultSpec['name'] ? 'selected' : '' }}>
                                    {{ $defaultSpec['name'] }} ({{ $defaultSpec['prefix'] }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('specialization')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Bio --}}
                <div class="space-y-1.5">
                    <label for="bio" class="block text-xs font-semibold text-slate-300">
                        Qualifications & Experience <span class="text-slate-500 font-normal">(optional)</span>
                    </label>
                    <textarea id="bio" name="bio" rows="3"
                        placeholder="Describe your field equipment, certifications, years of experience, notable cases…"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                        placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors resize-none">{{ old('bio') }}</textarea>
                    @error('bio')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                        <input id="password" type="password" name="password" required placeholder="Min. 6 chars"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                            focus:outline-none transition-colors
                            {{ $errors->has('password') ? 'border-red-500' : 'border-slate-700 focus:border-indigo-500' }}">
                        @error('password')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300">Confirm</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Repeat"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm placeholder-slate-600
                            focus:outline-none focus:border-indigo-500 transition-colors">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-2.5 rounded-xl font-bold text-sm bg-indigo-600 hover:bg-indigo-500 text-white
                    shadow-lg shadow-indigo-600/25 transition-all hover:-translate-y-0.5 transform">
                    Submit Application →
                </button>
            </form>
        </div>

        <div class="text-center text-xs text-slate-500 space-y-1">
            <div>Already have an account?
                <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">Sign In</a>
            </div>
            <div>Regular citizen?
                <a href="{{ route('register') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold ml-1">Citizen Registration</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        function formatPhone(val) {
            // Remove everything except numbers
            const digits = val.replace(/\D/g, '').substring(0, 11);
            let formatted = '';
            if (digits.length > 0) {
                formatted += digits.substring(0, 4);
            }
            if (digits.length > 4) {
                formatted += ' ' + digits.substring(4, 7);
            }
            if (digits.length > 7) {
                formatted += ' ' + digits.substring(7, 11);
            }
            return formatted;
        }

        phoneInput.addEventListener('input', function (e) {
            const cursorPosition = e.target.selectionStart;
            const previousLength = e.target.value.length;
            const formatted = formatPhone(e.target.value);
            e.target.value = formatted;

            // Preserve natural cursor placement when typing
            if (cursorPosition === previousLength) {
                e.target.selectionStart = e.target.selectionEnd = formatted.length;
            }
        });

        // Format on initial load if old value exists
        if (phoneInput.value) {
            phoneInput.value = formatPhone(phoneInput.value);
        }
    }

    // Badge input manual typing detection
    const badgeInputEl = document.getElementById('badge_number');
    const autoBadgeHidden = document.getElementById('badge_auto_generated');
    if (badgeInputEl && autoBadgeHidden) {
        badgeInputEl.addEventListener('input', function() {
            // Typing manually flags auto-generated as false so backend validation triggers required error
            autoBadgeHidden.value = '0';
        });
    }
});

function generateAutoBadge() {
    const specSelect = document.getElementById('specialization');
    let prefix = 'RSP';

    if (specSelect && specSelect.selectedIndex >= 0) {
        const selectedOption = specSelect.options[specSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset && selectedOption.dataset.prefix) {
            prefix = selectedOption.dataset.prefix;
        }
    }

    // Generate random 4-digit accreditation number
    const randomNum = Math.floor(1000 + Math.random() * 9000);
    const badge = prefix + '-' + randomNum;

    const badgeInput = document.getElementById('badge_number');
    const autoInput = document.getElementById('badge_auto_generated');

    if (badgeInput && autoInput) {
        badgeInput.value = badge;
        autoInput.value = '1';

        // Brief visual flash to confirm auto generation
        badgeInput.classList.add('ring-2', 'ring-indigo-400');
        setTimeout(() => {
            badgeInput.classList.remove('ring-2', 'ring-indigo-400');
        }, 500);
    }
}

function handleSpecializationChange() {
    const badgeInput = document.getElementById('badge_number');
    if (badgeInput && badgeInput.value) {
        generateAutoBadge();
    }
}
</script>
@endpush

