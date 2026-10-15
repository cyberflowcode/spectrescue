@extends('layouts.app')

@section('title', 'Citizen Registration')

@section('content')
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-10 px-4">
    <div class="w-full max-w-sm space-y-6">

        {{-- Header --}}
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-white">Create Citizen Account</h1>
                <p class="text-xs text-slate-400 mt-0.5">Report paranormal emergencies and track your cases</p>
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-7 shadow-2xl shadow-slate-950/60 space-y-4">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Full Name --}}
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-semibold text-slate-300">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                        required autofocus placeholder="Juan dela Cruz"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                        focus:outline-none transition-colors
                        {{ $errors->has('name') ? 'border-red-500' : 'border-slate-700 focus:border-cyan-500' }}">
                    @error('name')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-semibold text-slate-300">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        required placeholder="you@example.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                        focus:outline-none transition-colors
                        {{ $errors->has('email') ? 'border-red-500' : 'border-slate-700 focus:border-cyan-500' }}">
                    @error('email')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Phone --}}
                <div class="space-y-1.5">
                    <label for="phone" class="block text-xs font-semibold text-slate-300">
                        Phone Number <span class="text-slate-500 font-normal">(optional)</span>
                    </label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="09XX XXX XXXX" maxlength="13" inputmode="numeric"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm placeholder-slate-600
                        focus:outline-none focus:border-cyan-500 transition-colors font-mono">
                    @error('phone')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Password row --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                        <input id="password" type="password" name="password" required
                            placeholder="Min. 6 chars"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                            focus:outline-none transition-colors
                            {{ $errors->has('password') ? 'border-red-500' : 'border-slate-700 focus:border-cyan-500' }}">
                        @error('password')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300">Confirm</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            placeholder="Repeat"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm placeholder-slate-600
                            focus:outline-none focus:border-cyan-500 transition-colors">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-2.5 rounded-xl font-bold text-sm bg-cyan-500 hover:bg-cyan-400 text-slate-950
                    shadow-lg shadow-cyan-500/25 transition-all hover:-translate-y-0.5 transform mt-2">
                    Create Account →
                </button>
            </form>
        </div>

        <div class="text-center text-xs text-slate-500 space-y-1.5">
            <div>Already have an account?
                <a href="{{ route('login') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold ml-1">Sign In</a>
            </div>
            <div>Joining as a responder?
                <a href="{{ route('register.responder') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold ml-1">Responder Application</a>
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
            const digits = val.replace(/\D/g, '').substring(0, 11);
            let formatted = '';
            if (digits.length > 0) formatted += digits.substring(0, 4);
            if (digits.length > 4) formatted += ' ' + digits.substring(4, 7);
            if (digits.length > 7) formatted += ' ' + digits.substring(7, 11);
            return formatted;
        }

        phoneInput.addEventListener('input', function (e) {
            e.target.value = formatPhone(e.target.value);
        });

        if (phoneInput.value) {
            phoneInput.value = formatPhone(phoneInput.value);
        }
    }
});
</script>
@endpush

