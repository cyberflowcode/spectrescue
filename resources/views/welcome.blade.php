@extends('layouts.app')

@section('title', 'Emergency Operations & Paranormal Response')

@push('styles')
<style>
    /* Animated radar sweep for hero */
    @keyframes radar-sweep {
        from { transform: rotate(0deg); }
        to   { transform: rotate(360deg); }
    }
    .radar-sweep {
        animation: radar-sweep 4s linear infinite;
        transform-origin: center;
    }
    @keyframes float-up {
        0%, 100% { transform: translateY(0px); }
        50%       { transform: translateY(-8px); }
    }
    .float-anim { animation: float-up 3s ease-in-out infinite; }

    @keyframes text-glow {
        0%, 100% { text-shadow: 0 0 20px rgba(34,211,238,0.4); }
        50%       { text-shadow: 0 0 40px rgba(34,211,238,0.7), 0 0 80px rgba(99,102,241,0.3); }
    }
    .text-glow { animation: text-glow 3s ease-in-out infinite; }

    /* Scanline effect */
    .scanline::before {
        content: '';
        position: absolute;
        inset: 0;
        background: transparent;
        pointer-events: none;
        z-index: 1;
        border-radius: inherit;
    }
</style>
@endpush

@section('content')
<div class="space-y-16 pb-16">

    {{-- ===== HERO SECTION ===== --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900">

        {{-- Ambient Orbs --}}
        <div class="absolute -top-32 -right-32 w-[500px] h-[500px] rounded-full pointer-events-none bg-cyan-500/5"></div>
        <div class="absolute -bottom-32 -left-32 w-[500px] h-[500px] rounded-full pointer-events-none bg-indigo-500/5"></div>

        {{-- Radar SVG (decorative right side) --}}
        <div class="absolute right-8 top-1/2 -translate-y-1/2 w-48 h-48 sm:w-72 sm:h-72 lg:w-96 lg:h-96 opacity-10 pointer-events-none">
            <svg viewBox="0 0 200 200" class="w-full h-full">
                <circle cx="100" cy="100" r="90" fill="none" stroke="#22d3ee" stroke-width="0.5"/>
                <circle cx="100" cy="100" r="65" fill="none" stroke="#22d3ee" stroke-width="0.5"/>
                <circle cx="100" cy="100" r="40" fill="none" stroke="#22d3ee" stroke-width="0.5"/>
                <circle cx="100" cy="100" r="18" fill="none" stroke="#22d3ee" stroke-width="0.5"/>
                <line x1="100" y1="10" x2="100" y2="190" stroke="#22d3ee" stroke-width="0.3"/>
                <line x1="10" y1="100" x2="190" y2="100" stroke="#22d3ee" stroke-width="0.3"/>
                <g class="radar-sweep">
                    <path d="M 100 100 L 100 10 A 90 90 0 0 1 190 100 Z"
                        fill="#22d3ee" opacity="0.2"/>
                </g>
                <!-- Blips -->
                <circle cx="130" cy="60" r="3" fill="#22d3ee" opacity="0.8"/>
                <circle cx="75"  cy="130" r="2" fill="#f87171" opacity="0.9"/>
                <circle cx="155" cy="110" r="2.5" fill="#fbbf24" opacity="0.8"/>
            </svg>
        </div>

        {{-- Hero Content --}}
        <div class="relative z-10 px-8 sm:px-12 lg:px-16 py-14 sm:py-20 lg:py-24 max-w-3xl">

            {{-- Status badge --}}
            <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full mb-6
                border border-cyan-500/30 bg-cyan-500/10 text-cyan-400 text-xs font-mono uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                <span>24/7 Paranormal Rapid Response Dispatch · Active</span>
            </div>

            {{-- Main Headline --}}
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.05] mb-6">
                When the Unexplained<br class="hidden sm:block"> Strikes,
                <span class="text-glow text-cyan-400">
                    We&nbsp;Dispatch&nbsp;Relief.
                </span>
            </h1>

            <p class="text-slate-300 text-lg sm:text-xl leading-relaxed mb-8 max-w-xl">
                SPECTRESCUE is a centralized paranormal emergency management system connecting distressed citizens with
                certified exorcists, ghost hunters, and occult containment specialists.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-wrap gap-4">
                @auth
                    @if(Auth::user()->isDispatcher())
                        {{-- Dispatcher: Direct access to tactical console --}}
                        <a href="{{ route('dispatcher.dashboard') }}"
                            class="px-8 py-4 rounded-xl font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-xl shadow-amber-500/20
                            transition-all hover:-translate-y-1 flex items-center space-x-2.5 text-base">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <span>Access Dispatch Console</span>
                            <span class="text-lg leading-none">&rarr;</span>
                        </a>
                    @elseif(Auth::user()->isResponder())
                        {{-- Responder: Direct access to mission assignments --}}
                        <a href="{{ route('responder.dashboard') }}"
                            class="px-8 py-4 rounded-xl font-bold bg-cyan-600 hover:bg-cyan-500 text-white shadow-xl shadow-cyan-600/20
                            border border-cyan-500/40 transition-all hover:-translate-y-1 flex items-center space-x-2.5 text-base">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Access Mission Dashboard</span>
                            <span class="text-lg leading-none">&rarr;</span>
                        </a>
                    @elseif(Auth::user()->isAdmin())
                        {{-- Admin: HQ control center --}}
                        <a href="{{ route('admin.dashboard') }}"
                            class="px-8 py-4 rounded-xl font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-xl shadow-indigo-600/20
                            border border-indigo-500/40 transition-all hover:-translate-y-1 flex items-center space-x-2.5 text-base">
                            <span>Admin Control Center</span>
                            <span class="text-lg leading-none">&rarr;</span>
                        </a>
                    @else
                        {{-- Citizen: Primary reporting button + hub --}}
                        <a href="{{ route('citizen.incidents.create') }}"
                            class="px-7 py-4 rounded-xl font-bold bg-red-600 hover:bg-red-500 text-white shadow-xl shadow-red-600/30
                            transition-all hover:-translate-y-1 flex items-center space-x-2.5 text-base">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Report Paranormal Emergency</span>
                        </a>
                        <a href="{{ route('citizen.dashboard') }}"
                            class="px-7 py-4 rounded-xl font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200
                            border border-slate-700 hover:border-slate-600 transition-all hover:-translate-y-1 text-base">
                            My Reports Hub
                        </a>
                    @endif
                @else
                    {{-- Guest (Not logged in) --}}
                    <a href="{{ route('citizen.incidents.create') }}"
                        class="px-7 py-4 rounded-xl font-bold bg-red-600 hover:bg-red-500 text-white shadow-xl shadow-red-600/30
                        transition-all hover:-translate-y-1 flex items-center space-x-2.5 text-base">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Report Paranormal Emergency</span>
                    </a>

                    <a href="{{ route('register.responder') }}"
                        class="px-6 py-4 rounded-xl font-semibold text-cyan-400 hover:text-cyan-300
                        border border-cyan-500/30 hover:bg-cyan-500/10 transition-all hover:-translate-y-1 text-base">
                        Apply as Responder →
                    </a>
                @endauth
            </div>

            {{-- Live Metrics Bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-12 pt-8 border-t border-slate-800/60">
                <div class="space-y-0.5">
                    <div class="text-3xl font-extrabold font-mono text-cyan-400">{{ $stats['total'] ?? 0 }}</div>
                    <div class="text-xs uppercase tracking-wider text-slate-500 font-medium">Total Emergencies</div>
                </div>
                <div class="space-y-0.5">
                    <div class="text-3xl font-extrabold font-mono text-emerald-400">{{ $stats['resolved'] ?? 0 }}</div>
                    <div class="text-xs uppercase tracking-wider text-slate-500 font-medium">Entities Neutralized</div>
                </div>
                <div class="space-y-0.5">
                    <div class="text-3xl font-extrabold font-mono text-indigo-400">{{ $stats['responders'] ?? 0 }}</div>
                    <div class="text-xs uppercase tracking-wider text-slate-500 font-medium">Verified Specialists</div>
                </div>
                <div class="space-y-0.5">
                    <div class="text-3xl font-extrabold font-mono text-amber-400">100%</div>
                    <div class="text-xs uppercase tracking-wider text-slate-500 font-medium">Dispatch Readiness</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== HOW IT WORKS ===== --}}
    <div class="space-y-8">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-xs font-mono uppercase tracking-widest text-cyan-400">Operations Protocol</span>
            <h2 class="text-2xl sm:text-3xl font-bold text-white">How Emergency Dispatch Operates</h2>
            <p class="text-sm text-slate-400">Step-by-step lifecycle of a paranormal emergency — from initial call to spiritual containment.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
            $steps = [
                ['num'=>'1', 'color'=>'red',    'title'=>'Incident Reported',         'desc'=>'Distressed citizen files emergency details: threat type, address, and evidence photos through the secure portal.'],
                ['num'=>'2', 'color'=>'amber',  'title'=>'Evaluated &amp; Dispatched','desc'=>'Dispatcher evaluates threat level (Low → Critical) and assigns the closest verified paranormal specialist.'],
                ['num'=>'3', 'color'=>'cyan',   'title'=>'Response &amp; Containment','desc'=>'Responder accepts the dispatch call, arrives on-site with tools, and executes the containment protocol.'],
                ['num'=>'4', 'color'=>'emerald','title'=>'Report &amp; Resolution',   'desc'=>'Responder submits official containment findings. The emergency is marked resolved and archived as a dossier.'],
            ];
            @endphp
            @foreach($steps as $i => $step)
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-3 relative
                hover:border-{{ $step['color'] }}-500/40 hover:shadow-lg hover:shadow-{{ $step['color'] }}-950/30 transition-all group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center font-bold text-lg font-mono
                    bg-{{ $step['color'] }}-500/15 text-{{ $step['color'] }}-400 border border-{{ $step['color'] }}-500/30
                    group-hover:scale-110 transition-transform">
                    {{ $step['num'] }}
                </div>
                <h3 class="font-bold text-white text-base">{!! $step['title'] !!}</h3>
                <p class="text-xs text-slate-400 leading-relaxed">{!! $step['desc'] !!}</p>

                {{-- Step connector --}}
                @if($i < count($steps) - 1)
                <div class="hidden lg:block absolute -right-2.5 top-1/2 -translate-y-1/2 z-10">
                    <div class="w-5 h-5 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- ===== ROLE CARDS ===== --}}
    <div class="space-y-6">
        <div class="text-center space-y-2">
            <span class="text-xs font-mono uppercase tracking-widest text-indigo-400">Role-Based Access</span>
            <h2 class="text-2xl font-bold text-white">Every Role Has Its Mission</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="p-6 rounded-2xl border border-slate-700/60 bg-slate-900 space-y-3 hover:border-slate-600 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-slate-700/50 border border-slate-600/50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-white">Citizen</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Report paranormal incidents, upload evidence, and track real-time dispatch status from your personal hub.</p>
                @auth
                    @if(Auth::user()->isCitizen())
                        <a href="{{ route('citizen.dashboard') }}" class="inline-flex text-xs font-semibold text-slate-300 hover:text-white hover:underline">
                            Open Citizen Hub →
                        </a>
                    @else
                        <span class="inline-flex text-xs text-slate-500 font-mono">Citizen Account</span>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="inline-flex text-xs font-semibold text-slate-300 hover:text-white hover:underline">
                        Register as Citizen →
                    </a>
                @endauth
            </div>

            <div class="p-6 rounded-2xl border border-amber-500/20 bg-slate-900 space-y-3 hover:border-amber-500/50 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <h3 class="font-bold text-white">Dispatcher</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Triage incoming calls on the tactical radar map, classify threat levels, and dispatch certified specialists to emergencies.</p>
                @auth
                    @if(Auth::user()->isDispatcher() || Auth::user()->isAdmin())
                        <a href="{{ route('dispatcher.dashboard') }}" class="inline-flex text-xs font-semibold text-amber-400 hover:text-amber-300 hover:underline">
                            Access Console →
                        </a>
                    @else
                        <span class="inline-flex text-xs text-slate-500 font-mono">Dispatcher Only</span>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex text-xs font-semibold text-amber-400 hover:text-amber-300 hover:underline">
                        Access Console →
                    </a>
                @endauth
            </div>

            <div class="p-6 rounded-2xl border border-cyan-500/20 bg-slate-900 space-y-3 hover:border-cyan-500/50 transition-colors">
                <div class="w-11 h-11 rounded-xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-white">Responder</h3>
                <p class="text-xs text-slate-400 leading-relaxed">Accept field assignments, update mission status, submit containment reports, and build your verified specialist profile.</p>
                @auth
                    @if(Auth::user()->isResponder())
                        <a href="{{ route('responder.dashboard') }}" class="inline-flex text-xs font-semibold text-cyan-400 hover:text-cyan-300 hover:underline">
                            Open Mission Dashboard →
                        </a>
                    @else
                        <span class="inline-flex text-xs text-slate-500 font-mono">Specialist Role</span>
                    @endif
                @else
                    <a href="{{ route('register.responder') }}" class="inline-flex text-xs font-semibold text-cyan-400 hover:text-cyan-300 hover:underline">
                        Apply as Responder →
                    </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- ===== RECENT INCIDENTS TICKER ===== --}}
    @if(isset($recentIncidents) && $recentIncidents->count() > 0)
    <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-white text-lg">Active Incident Ticker</h3>
                <p class="text-xs text-slate-400">Live feed of reported events across metropolitan districts</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span>
                <span class="text-xs font-mono text-red-400">LIVE</span>
            </div>
        </div>

        <div class="divide-y divide-slate-800">
            @foreach($recentIncidents as $incident)
            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs text-cyan-400 font-semibold">{{ $incident->tracking_code }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-mono {{ $incident->threatBadgeClass() }}">{{ $incident->threat_level }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-mono {{ $incident->statusBadgeClass() }}">{{ $incident->status }}</span>
                    </div>
                    <div class="text-sm font-semibold text-slate-200">{{ $incident->title }}</div>
                    <div class="text-xs text-slate-400">{{ $incident->location_address }}, {{ $incident->location_city }}</div>
                </div>
                <div class="text-xs font-mono text-slate-500 shrink-0">
                    {{ $incident->created_at->diffForHumans() }}
                </div>
            </div>
            @endforeach
        </div>

        <div class="pt-2 text-center">
            @auth
                @if(Auth::user()->isDispatcher())
                    <a href="{{ route('dispatcher.dashboard') }}" class="text-xs font-mono text-cyan-400 hover:text-cyan-300 hover:underline">
                        View active operations in Dispatch Console →
                    </a>
                @elseif(Auth::user()->isAdmin())
                    <a href="{{ route('admin.incidents.index') }}" class="text-xs font-mono text-cyan-400 hover:text-cyan-300 hover:underline">
                        Manage full incident archive in Admin Panel →
                    </a>
                @elseif(Auth::user()->isResponder())
                    <a href="{{ route('responder.dashboard') }}" class="text-xs font-mono text-cyan-400 hover:text-cyan-300 hover:underline">
                        View field assignments in Mission Dashboard →
                    </a>
                @else
                    <a href="{{ route('citizen.incidents.index') }}" class="text-xs font-mono text-cyan-400 hover:text-cyan-300 hover:underline">
                        View your incident history in Citizen Hub →
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="text-xs font-mono text-cyan-400 hover:text-cyan-300 hover:underline">
                    Login to view full incident archive →
                </a>
            @endauth
        </div>
    </div>
    @endif

</div>
@endsection
