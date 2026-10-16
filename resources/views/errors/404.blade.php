<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Page Not Found | SPECTRESCUE</title>
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
        <div class="inline-flex items-center space-x-2 text-xs font-mono uppercase tracking-widest text-cyan-400 bg-cyan-950/60 border border-cyan-800/60 px-3.5 py-1.5 rounded-full">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
            <span>SPECTRESCUE ANOMALY</span>
        </div>

        <div class="relative inline-block">
            <div class="w-24 h-24 rounded-3xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center mx-auto shadow-2xl shadow-cyan-500/20">
                <svg class="w-12 h-12 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="absolute -bottom-2 -right-2 px-2.5 py-0.5 rounded-lg bg-cyan-500 text-slate-950 text-xs font-black tracking-wider uppercase shadow">
                404
            </span>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Signal Lost in Spectral Void</h1>
            <p class="text-sm text-slate-400 leading-relaxed">
                The coordinates or record you are trying to access do not exist in the dispatch database or have been archived.
            </p>
        </div>

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
                    <span>Return to Dashboard</span>
                </a>
            @else
                <a href="{{ url('/') }}"
                    class="w-full inline-flex items-center justify-center space-x-2 py-3 px-4 rounded-xl font-bold text-sm bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/25 transition-all">
                    <span>Back to Home</span>
                </a>
            @endauth
        </div>
    </div>

</body>
</html>
