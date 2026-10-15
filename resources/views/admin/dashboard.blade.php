@extends('layouts.app')

@section('title', 'Admin Control Center')

@section('content')
<div class="space-y-6 max-w-full">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-indigo-400">Headquarters Control Center</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Agency Analytics &amp; Control</h1>
            <p class="text-sm text-slate-400 mt-0.5">Full system overview — users, incidents, responders, threats</p>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('admin.reports.index') }}"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>System Reports</span>
            </a>
        </div>
    </div>

    {{-- ===== STAT CARDS ROW ===== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Users --}}
        <div class="p-5 rounded-2xl bg-slate-900 border border-indigo-500/20 relative overflow-hidden group hover:border-indigo-500/50 transition-colors">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-indigo-500/5 group-hover:bg-indigo-500/10 transition-colors"></div>
            <div class="text-xs uppercase tracking-wider font-mono text-indigo-400">Total Users</div>
            <div class="text-4xl font-extrabold font-mono text-white mt-2 leading-none">{{ $stats['total_users'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1.5 flex items-center space-x-3 font-mono">
                <span class="text-slate-300">{{ $stats['citizens'] }}C</span>
                <span class="text-amber-400">{{ $stats['dispatchers'] }}D</span>
                <span class="text-cyan-400">{{ $stats['responders'] }}R</span>
            </div>
        </div>

        {{-- Total Incidents --}}
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-700/50 relative overflow-hidden group hover:border-slate-600 transition-colors">
            <div class="absolute -right-4 -top-4 w-20 h-20 rounded-full bg-slate-700/10 group-hover:bg-slate-700/20 transition-colors"></div>
            <div class="text-xs uppercase tracking-wider font-mono text-slate-400">Total Incidents</div>
            <div class="text-4xl font-extrabold font-mono text-white mt-2 leading-none">{{ $stats['total_incidents'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1.5">Logged across all sectors</div>
        </div>

        {{-- Active Missions --}}
        <div class="p-5 rounded-2xl bg-slate-900 border border-cyan-500/20 relative overflow-hidden group hover:border-cyan-500/50 transition-colors">
            <div class="flex items-center space-x-2">
                <span class="text-xs uppercase tracking-wider font-mono text-cyan-400">Active Missions</span>
                @if($stats['active_missions'] > 0)
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                @endif
            </div>
            <div class="text-4xl font-extrabold font-mono text-cyan-300 mt-2 leading-none">{{ $stats['active_missions'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1.5">Dispatched &amp; responding</div>
        </div>

        {{-- Resolved --}}
        <div class="p-5 rounded-2xl bg-slate-900 border border-emerald-500/20 relative overflow-hidden group hover:border-emerald-500/50 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-emerald-400">Cases Neutralized</div>
            <div class="text-4xl font-extrabold font-mono text-emerald-300 mt-2 leading-none">{{ $stats['resolved_incidents'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1.5">Successfully closed &amp; archived</div>
        </div>
    </div>

    {{-- ===== ADMIN CONTROL HUB (Flowchart Modules) ===== --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                <h2 class="text-xs font-mono uppercase tracking-widest text-slate-400 font-bold">Admin Management Modules</h2>
            </div>
            <span class="text-[11px] font-mono text-slate-500">Full System Administration</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- 1. Manage Users --}}
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-indigo-500/40 transition-all flex flex-col justify-between group shadow-lg">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500/20 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-400">{{ $stats['total_users'] }} Users</span>
                    </div>
                    <h3 class="font-bold text-white text-base mt-3">Manage Users</h3>
                    <p class="text-xs text-slate-400 mt-1">View, add, edit, and deactivate system accounts across all roles.</p>
                </div>
                <div class="flex items-center gap-2 mt-4 pt-3 border-t border-slate-800/80">
                    <a href="{{ route('admin.users.index') }}" class="flex-1 py-1.5 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium text-center transition-colors">
                        View Users
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="py-1.5 px-3 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-colors">
                        + Add
                    </a>
                </div>
            </div>

            {{-- 2. Verify Responders --}}
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-cyan-500/40 transition-all flex flex-col justify-between group shadow-lg">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 group-hover:bg-cyan-500/20 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        @if($stats['pending_responders'] > 0)
                            <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-mono font-bold">
                                {{ $stats['pending_responders'] }} Pending
                            </span>
                        @else
                            <span class="text-xs font-mono text-emerald-400">All Verified</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-white text-base mt-3">Verify Responders</h3>
                    <p class="text-xs text-slate-400 mt-1">Review specialist licenses, credentials, and approve/reject applications.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <a href="{{ route('admin.responders.index') }}" class="block w-full py-1.5 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium text-center transition-colors">
                        Review Applications &rarr;
                    </a>
                </div>
            </div>

            {{-- 3. Manage Incidents --}}
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-amber-500/40 transition-all flex flex-col justify-between group shadow-lg">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 group-hover:bg-amber-500/20 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-400">{{ $stats['total_incidents'] }} Total</span>
                    </div>
                    <h3 class="font-bold text-white text-base mt-3">Manage Incidents</h3>
                    <p class="text-xs text-slate-400 mt-1">View, update status, and monitor live paranormal emergency dispatches.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <a href="{{ route('admin.incidents.index') }}" class="block w-full py-1.5 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium text-center transition-colors">
                        Incident Registry &rarr;
                    </a>
                </div>
            </div>

            {{-- 4. System Reports --}}
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 transition-all flex flex-col justify-between group shadow-lg">
                <div>
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:bg-emerald-500/20 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-mono font-bold text-emerald-400">{{ $stats['resolved_incidents'] }} Resolved</span>
                    </div>
                    <h3 class="font-bold text-white text-base mt-3">System Reports</h3>
                    <p class="text-xs text-slate-400 mt-1">Access resolution dossiers, containment analytics, and printable reports.</p>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <a href="{{ route('admin.reports.index') }}" class="block w-full py-1.5 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium text-center transition-colors">
                        View Dossiers &rarr;
                    </a>
                </div>
            </div>
        </div>

        {{-- Secondary Modules Row: In-App Notifications & System Settings --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 flex items-center justify-between group transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-white text-sm">In-App Notifications</div>
                        <div class="text-[11px] text-slate-400">Send announcements or emergency broadcasts</div>
                    </div>
                </div>
                <a href="{{ route('admin.notifications.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-indigo-300 text-xs font-medium transition-colors">
                    Compose &rarr;
                </a>
            </div>

            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 flex items-center justify-between group transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-white text-sm">System Settings</div>
                        <div class="text-[11px] text-slate-400">Configure hotline, agency info, and operational parameters</div>
                    </div>
                </div>
                <a href="{{ route('admin.settings.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition-colors">
                    Manage &rarr;
                </a>
            </div>
        </div>
    </div>

    {{-- ===== MIDDLE ROW: Threat Breakdown + User Roles ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Threat Level Breakdown (visual bars) --}}
        <div class="lg:col-span-2 rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-5">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-white text-base">Threat Level Distribution</h3>
                <span class="text-xs font-mono text-slate-500">{{ $stats['total_incidents'] }} total</span>
            </div>

            @php
                $maxThreat = max(1, $stats['critical_threats'], $stats['high_threats'], $stats['medium_threats'], $stats['low_threats']);
            @endphp

            {{-- Critical --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-mono font-bold text-red-400 uppercase">CRITICAL</span>
                    <span class="font-mono font-bold text-red-300">{{ $stats['critical_threats'] }}</span>
                </div>
                <div class="h-2.5 rounded-full bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-red-500 transition-all duration-700"
                        style="width: {{ $stats['total_incidents'] > 0 ? round(($stats['critical_threats'] / $stats['total_incidents']) * 100) : 0 }}%"></div>
                </div>
            </div>
            {{-- High --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-mono font-bold text-orange-400 uppercase">HIGH</span>
                    <span class="font-mono font-bold text-orange-300">{{ $stats['high_threats'] }}</span>
                </div>
                <div class="h-2.5 rounded-full bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-orange-500 transition-all duration-700"
                        style="width: {{ $stats['total_incidents'] > 0 ? round(($stats['high_threats'] / $stats['total_incidents']) * 100) : 0 }}%"></div>
                </div>
            </div>
            {{-- Medium --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-mono font-bold text-yellow-400 uppercase">MEDIUM</span>
                    <span class="font-mono font-bold text-yellow-300">{{ $stats['medium_threats'] }}</span>
                </div>
                <div class="h-2.5 rounded-full bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-yellow-500 transition-all duration-700"
                        style="width: {{ $stats['total_incidents'] > 0 ? round(($stats['medium_threats'] / $stats['total_incidents']) * 100) : 0 }}%"></div>
                </div>
            </div>
            {{-- Low --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-mono font-bold text-emerald-400 uppercase">LOW</span>
                    <span class="font-mono font-bold text-emerald-300">{{ $stats['low_threats'] }}</span>
                </div>
                <div class="h-2.5 rounded-full bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500 transition-all duration-700"
                        style="width: {{ $stats['total_incidents'] > 0 ? round(($stats['low_threats'] / $stats['total_incidents']) * 100) : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- User Role Breakdown + Responders --}}
        <div class="space-y-4">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 space-y-4">
                <h3 class="font-bold text-white text-sm">User Role Breakdown</h3>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-2 text-xs text-slate-300">
                            <span class="w-2.5 h-2.5 rounded-sm bg-slate-500"></span>
                            <span>Citizens</span>
                        </span>
                        <span class="font-mono font-bold text-white text-sm">{{ $stats['citizens'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-2 text-xs text-amber-300">
                            <span class="w-2.5 h-2.5 rounded-sm bg-amber-500"></span>
                            <span>Dispatchers</span>
                        </span>
                        <span class="font-mono font-bold text-amber-300 text-sm">{{ $stats['dispatchers'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-2 text-xs text-cyan-300">
                            <span class="w-2.5 h-2.5 rounded-sm bg-cyan-500"></span>
                            <span>Responders</span>
                        </span>
                        <span class="font-mono font-bold text-cyan-300 text-sm">{{ $stats['responders'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center space-x-2 text-xs text-indigo-300">
                            <span class="w-2.5 h-2.5 rounded-sm bg-indigo-500"></span>
                            <span>Admins</span>
                        </span>
                        <span class="font-mono font-bold text-indigo-300 text-sm">{{ $stats['admins'] }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.users.index') }}"
                    class="block w-full text-center text-xs font-bold py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                    Manage All Users →
                </a>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-amber-500/20 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-white text-sm">Responder Status</h3>
                    @if($stats['pending_responders'] > 0)
                        <span class="flex items-center space-x-1.5 text-xs font-mono text-amber-400 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span>{{ $stats['pending_responders'] }} pending</span>
                        </span>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                        <div class="text-xl font-extrabold font-mono text-emerald-300">{{ $stats['approved_responders'] }}</div>
                        <div class="text-[10px] text-emerald-400 font-mono uppercase mt-0.5">Approved</div>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-center">
                        <div class="text-xl font-extrabold font-mono text-amber-300">{{ $stats['pending_responders'] }}</div>
                        <div class="text-[10px] text-amber-400 font-mono uppercase mt-0.5">Pending</div>
                    </div>
                </div>
                <a href="{{ route('admin.responders.index', ['status' => 'pending']) }}"
                    class="block w-full text-center text-xs font-bold py-2 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 text-amber-400 border border-amber-500/30 transition-colors">
                    Review Applications →
                </a>
            </div>
        </div>
    </div>

    {{-- ===== PENDING RESPONDERS QUEUE ===== --}}
    <div class="rounded-2xl bg-slate-900 border border-indigo-500/30 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 bg-indigo-950/20 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-400 animate-pulse"></span>
                <h2 class="font-bold text-white text-base">
                    Responder Verification Queue
                    @if($pendingResponders->count() > 0)
                        <span class="ml-2 px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 text-xs font-mono">{{ $pendingResponders->count() }}</span>
                    @endif
                </h2>
            </div>
            <a href="{{ route('admin.responders.index') }}" class="text-xs font-mono text-indigo-400 hover:text-indigo-300 hover:underline">
                View All →
            </a>
        </div>

        @if($pendingResponders->count() > 0)
            {{-- Desktop table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-mono text-xs uppercase">
                            <th class="py-3 px-6">Applicant</th>
                            <th class="py-3 px-6">Badge No.</th>
                            <th class="py-3 px-6">Specialization</th>
                            <th class="py-3 px-6">Bio Statement</th>
                            <th class="py-3 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @foreach($pendingResponders as $responder)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-white">{{ $responder->user->name }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $responder->user->email }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $responder->user->phone }}</div>
                            </td>
                            <td class="py-4 px-6 font-mono text-sm font-bold text-cyan-400">
                                {{ $responder->badge_number }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-200">
                                {{ $responder->specialization }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400 max-w-xs">
                                <span class="line-clamp-2">{{ $responder->bio ?? 'No statement provided' }}</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <form method="POST" action="{{ route('admin.responders.verify', $responder->id) }}">
                                        @csrf
                                        <input type="hidden" name="verification_status" value="approved">
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow shadow-emerald-500/20 transition-all">
                                            ✓ Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.responders.verify', $responder->id) }}">
                                        @csrf
                                        <input type="hidden" name="verification_status" value="rejected">
                                        <button type="submit"
                                            onclick="return confirm('Reject {{ $responder->user->name }}\'s application?');"
                                            class="px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800 hover:bg-red-500/20 text-slate-400 hover:text-red-300 border border-slate-700 transition-colors">
                                            ✕ Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Mobile cards --}}
            <div class="sm:hidden divide-y divide-slate-800">
                @foreach($pendingResponders as $responder)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-bold text-white">{{ $responder->user->name }}</div>
                            <div class="text-xs text-cyan-400 font-mono">{{ $responder->badge_number }}</div>
                            <div class="text-xs text-slate-400">{{ $responder->specialization }}</div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 text-xs font-mono border border-amber-500/40">Pending</span>
                    </div>
                    <div class="flex space-x-2">
                        <form method="POST" action="{{ route('admin.responders.verify', $responder->id) }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="verification_status" value="approved">
                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950">✓ Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.responders.verify', $responder->id) }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="verification_status" value="rejected">
                            <button type="submit" onclick="return confirm('Reject?');" class="w-full py-2 rounded-xl text-xs font-medium bg-slate-800 text-red-400 border border-slate-700">✕ Reject</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-10 text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-xs text-slate-400">No pending responder applications. All specialists are processed.</p>
            </div>
        @endif
    </div>

    {{-- ===== RECENT INCIDENT ACTIVITY ===== --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-base">Recent Emergency Activity</h3>
            <a href="{{ route('admin.incidents.index') }}" class="text-xs font-mono text-cyan-400 hover:text-cyan-300 hover:underline">
                Full Archive →
            </a>
        </div>
        <div class="divide-y divide-slate-800">
            @forelse($recentIncidents as $incident)
            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-800/30 transition-colors">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->threatBadgeClass() }}">{{ $incident->threat_level }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->statusBadgeClass() }}">{{ $incident->status }}</span>
                    </div>
                    <div class="font-semibold text-white text-sm">{{ $incident->title }}</div>
                    <div class="text-xs text-slate-400">
                        {{ $incident->location_address }}, {{ $incident->location_city }}
                        &bull; <span class="text-slate-500">{{ $incident->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <a href="{{ route('dispatcher.incidents.show', $incident->id) }}"
                    class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-medium transition-colors shrink-0">
                    Inspect →
                </a>
            </div>
            @empty
            <div class="p-10 text-center text-xs text-slate-500">No incidents logged yet.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection
