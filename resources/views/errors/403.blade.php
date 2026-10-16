<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Access Restricted | SPECTRESCUE</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-cyan-500 selection:text-slate-950">

    <div class="max-w-md w-full text-center space-y-6">
        {{-- Brand --}}
        <div class="inline-flex items-center space-x-2 text-xs font-mono uppercase tracking-widest text-cyan-400 bg-cyan-950/60 border border-cyan-800/60 px-3.5 py-1.5 rounded-full">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
            <span>SPECTRESCUE SECURITY GATE</span>
        </div>

        {{-- Icon & Status Code --}}
        <div class="relative inline-block">
            <div class="w-24 h-24 rounded-3xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mx-auto shadow-2xl shadow-amber-500/20">
                <svg class="w-12 h-12 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <span class="absolute -bottom-2 -right-2 px-2.5 py-0.5 rounded-lg bg-amber-500 text-slate-950 text-xs font-black tracking-wider uppercase shadow">
                403
            </span>
        </div>

        {{-- Title & Message --}}
        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Security Clearance Restricted</h1>
            <p class="text-sm text-slate-400 leading-relaxed">
                {{ $exception->getMessage() ?: 'Your current account role does not have authorization to view this section of the system.' }}
            </p>
        </div>

        {{-- Logged in user info badge --}}
        @auth
            <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-xs text-left space-y-1.5">
                <div class="text-[11px] font-mono uppercase text-slate-400">Current Active Session:</div>
                <div class="flex items-center justify-between font-semibold text-slate-200">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-slate-800 text-cyan-300 font-mono text-[11px] uppercase border border-slate-700">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>
        @endauth

        {{-- Action Buttons --}}
        <div class="space-y-2.5 pt-2">
            @auth
                @php
                    $roleRoute = match(auth()->user()->role) {
                        'admin'      => route('admin.dashboard'),
                        'dispatcher' => route('dispatcher.dashboard'),
                        'responder'  => route('responder.dashboard'),
                        default      => route('citizen.dashboard'),
                    };
                @endphp

                <a href="{{ $roleRoute }}"
                    class="w-full inline-flex items-center justify-center space-x-2 py-3 px-4 rounded-xl font-bold text-sm bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/25 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Return to {{ ucfirst(auth()->user()->role) }} Dashboard</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="inline-block w-full">
                    @csrf
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl font-medium text-xs text-slate-400 hover:text-white bg-slate-900/60 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition-colors">
                        Sign Out &amp; Switch Account
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="w-full inline-flex items-center justify-center space-x-2 py-3 px-4 rounded-xl font-bold text-sm bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/25 transition-all">
                    <span>Log In to SPECTRESCUE</span>
                </a>
            @endauth
        </div>

        <p class="text-[11px] text-slate-400">
            Need multiple accounts open at once? Use an <span class="text-slate-300 font-semibold">Incognito / Private tab</span> or a different browser profile.
        </p>
    </div>

</body>
</html>
