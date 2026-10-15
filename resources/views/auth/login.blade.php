@extends('layouts.app')

@section('title', 'Log In')

@section('content')
<div class="flex-1 flex items-center justify-center my-auto py-2">
    <div class="w-full max-w-sm sm:max-w-md space-y-4">

        {{-- Logo --}}
        <div class="text-center space-y-1.5">
            <div class="w-12 h-12 rounded-2xl bg-cyan-600 flex items-center justify-center mx-auto shadow-lg shadow-cyan-600/25">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-extrabold text-white tracking-tight">Welcome back</h1>
                <p class="text-xs text-slate-400">Sign in to your SPECTRESCUE account</p>
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl shadow-slate-950/60 space-y-4">
            <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
                @csrf

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-semibold text-slate-300">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        required autofocus autocomplete="email"
                        placeholder="your@email.com"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                        transition-colors focus:outline-none
                        {{ $errors->has('email') ? 'border-red-500 focus:border-red-400' : 'border-slate-700 focus:border-cyan-500' }}">
                    @error('email')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                    <input id="password" type="password" name="password"
                        required autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm placeholder-slate-600
                        focus:outline-none focus:border-cyan-500 transition-colors">
                    @error('password')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember --}}
                <!-- <label class="flex items-center space-x-2.5 cursor-pointer select-none group">
                    <input type="checkbox" name="remember"
                        class="w-4 h-4 rounded bg-slate-950 border-slate-600 text-cyan-500 focus:ring-cyan-500 focus:ring-1">
                    <span class="text-xs text-slate-400 group-hover:text-slate-300 transition-colors">Keep me signed in</span>
                </label> -->

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-2.5 rounded-xl font-bold text-sm bg-cyan-500 hover:bg-cyan-400 text-slate-950
                    shadow-lg shadow-cyan-500/25 transition-all hover:-translate-y-0.5 transform">
                    Sign In →
                </button>
            </form>
        </div>

        {{-- Footer links --}}
        <div class="text-center text-xs text-slate-500 space-y-1.5">
            <div>
                Don't have an account?
                <a href="{{ route('register') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold hover:underline ml-1">Register as Citizen</a>
            </div>
            <div>
                Ghost hunter or exorcist?
                <a href="{{ route('register.responder') }}" class="text-indigo-400 hover:text-indigo-300 font-semibold hover:underline ml-1">Apply as Responder</a>
            </div>
        </div>
    </div>
</div>
@endsection
