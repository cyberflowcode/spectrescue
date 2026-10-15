@extends('layouts.app')

@section('title', 'User Dossier: ' . $user->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Top Navigation & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.users.index') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
            <span>&larr; Back to Manage Users</span>
        </a>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.users.edit', $user->id) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit User Account</span>
            </a>

            @if(!$user->isAdmin())
                <form method="POST" action="{{ route('admin.users.toggle_status', $user->id) }}">
                    @csrf
                    <button type="submit"
                        onclick="return confirm('{{ $user->is_active ? 'Deactivate' : 'Reactivate' }} account for {{ $user->name }}?');"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors border
                        {{ $user->is_active
                            ? 'bg-slate-900 hover:bg-red-500/20 text-red-400 hover:text-red-300 border-red-500/30'
                            : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border-emerald-500/30' }}">
                        {{ $user->is_active ? 'Deactivate Account' : 'Reactivate Account' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- User Header Banner --}}
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-bold shrink-0
                @if($user->isAdmin()) bg-indigo-500/20 text-indigo-300 border border-indigo-500/40
                @elseif($user->isDispatcher()) bg-amber-500/20 text-amber-300 border border-amber-500/40
                @elseif($user->isResponder()) bg-cyan-500/20 text-cyan-300 border border-cyan-500/40
                @else bg-slate-800 text-slate-300 border border-slate-700 @endif">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex items-center space-x-2.5">
                    <h1 class="text-2xl font-bold text-white">{{ $user->name }}</h1>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-mono font-bold uppercase
                        @if($user->role === 'admin') bg-indigo-500/20 text-indigo-300 border border-indigo-500/40
                        @elseif($user->role === 'dispatcher') bg-amber-500/20 text-amber-300 border border-amber-500/40
                        @elseif($user->role === 'responder') bg-cyan-500/20 text-cyan-300 border border-cyan-500/40
                        @else bg-slate-800 text-slate-300 border border-slate-700 @endif">
                        {{ $user->role }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 font-mono mt-1">
                    <span>{{ $user->email }}</span>
                    <span>&bull;</span>
                    <span>{{ $user->phone ?? 'No phone logged' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3 shrink-0">
            @if($user->is_active)
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Active Account</span>
                </span>
            @else
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-red-500/10 text-red-400 border border-red-500/30">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                    <span>Deactivated</span>
                </span>
            @endif
        </div>
    </div>

    {{-- Detail Cards Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Account Details (1 Col) --}}
        <div class="space-y-6">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 space-y-4 shadow-xl">
                <h3 class="text-xs font-mono uppercase tracking-wider text-slate-400 font-bold border-b border-slate-800 pb-2.5">
                    Account Specifications
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-500 block text-[11px] font-mono uppercase">User ID</span>
                        <span class="text-white font-mono font-semibold">#{{ $user->id }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px] font-mono uppercase">Full Name</span>
                        <span class="text-white font-medium">{{ $user->name }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px] font-mono uppercase">Email Address</span>
                        <span class="text-cyan-400 font-mono">{{ $user->email }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px] font-mono uppercase">Contact Number</span>
                        <span class="text-slate-200 font-mono">{{ $user->phone ?? 'Not provided' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px] font-mono uppercase">Assigned System Role</span>
                        <span class="text-white font-bold uppercase font-mono">{{ $user->role }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px] font-mono uppercase">Registered Date</span>
                        <span class="text-slate-300 font-mono">{{ $user->created_at->format('M d, Y h:i A') }} ({{ $user->created_at->diffForHumans() }})</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block text-[11px] font-mono uppercase">Last Updated</span>
                        <span class="text-slate-300 font-mono">{{ $user->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Activity Summary Card --}}
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 space-y-3 shadow-xl">
                <h3 class="text-xs font-mono uppercase tracking-wider text-slate-400 font-bold border-b border-slate-800 pb-2.5">
                    Activity Metrics
                </h3>
                <div class="grid grid-cols-2 gap-3 text-center">
                    @if($user->isCitizen())
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <div class="text-2xl font-bold font-mono text-cyan-400">{{ $stats['reported_incidents_count'] }}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-mono mt-1">Emergencies Reported</div>
                        </div>
                    @elseif($user->isResponder())
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <div class="text-2xl font-bold font-mono text-cyan-400">{{ $stats['responder_assignments_count'] }}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-mono mt-1">Dispatches</div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <div class="text-2xl font-bold font-mono text-emerald-400">{{ $stats['responder_resolved_count'] }}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-mono mt-1">Resolved Reports</div>
                        </div>
                    @elseif($user->isDispatcher())
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 col-span-2">
                            <div class="text-2xl font-bold font-mono text-amber-400">{{ $stats['dispatched_missions_count'] }}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-mono mt-1">Field Dispatches Authorized</div>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 col-span-2">
                            <div class="text-sm font-bold text-indigo-400">System Administrator</div>
                            <div class="text-[10px] text-slate-400 uppercase font-mono mt-1">Full Administrative Clearance</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Role Details & History (2 Cols) --}}
        <div class="lg:col-span-2 space-y-6">

            @if($user->isResponder())
                {{-- Responder Profile Section --}}
                <div class="rounded-2xl bg-slate-900 border border-cyan-500/30 p-6 space-y-5 shadow-xl">
                    <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Field Specialist Profile</span>
                            <h2 class="text-lg font-bold text-white mt-1">Accreditation Details</h2>
                        </div>
                        @if($user->responderProfile)
                            <span class="text-xs px-2.5 py-1 rounded-full font-mono font-bold
                                @if($user->responderProfile->verification_status === 'approved') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @elseif($user->responderProfile->verification_status === 'rejected') bg-red-500/20 text-red-300 border border-red-500/30
                                @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
                                {{ ucfirst($user->responderProfile->verification_status) }}
                            </span>
                        @endif
                    </div>

                    @if($user->responderProfile)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                                <span class="font-mono uppercase text-slate-500 text-[10px] block">Badge Serial Number</span>
                                <div class="text-base font-bold text-cyan-400 font-mono">{{ $user->responderProfile->badge_number }}</div>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                                <span class="font-mono uppercase text-slate-500 text-[10px] block">Specialization Field</span>
                                <div class="text-base font-bold text-white">{{ $user->responderProfile->specialization }}</div>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                                <span class="font-mono uppercase text-slate-500 text-[10px] block">Availability Status</span>
                                @if($user->responderProfile->is_available)
                                    <div class="text-sm font-bold text-emerald-400 font-mono">● Available for Dispatch</div>
                                @else
                                    <div class="text-sm font-bold text-amber-400 font-mono">● Deployed / On Active Mission</div>
                                @endif
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                                <span class="font-mono uppercase text-slate-500 text-[10px] block">Verified Timestamp</span>
                                <div class="text-sm font-medium text-slate-300 font-mono">
                                    {{ $user->responderProfile->verified_at ? $user->responderProfile->verified_at->format('M d, Y h:i A') : 'Pending verification' }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <span class="text-xs font-mono uppercase text-slate-400">Operational Bio / Dossier</span>
                            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 leading-relaxed whitespace-pre-line">
                                {{ $user->responderProfile->bio ?? 'No operational bio provided.' }}
                            </div>
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-slate-500 rounded-xl bg-slate-950 border border-slate-800">
                            No responder accreditation profile found for this user account.
                        </div>
                    @endif
                </div>

                {{-- Recent Dispatch Assignments --}}
                @if(!empty($recentAssignments) && $recentAssignments->count() > 0)
                    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4 shadow-xl">
                        <h3 class="font-bold text-white text-base">Recent Field Missions ({{ $recentAssignments->count() }})</h3>
                        <div class="space-y-3">
                            @foreach($recentAssignments as $assignment)
                                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-xs flex items-center justify-between gap-3">
                                    <div>
                                        <div class="font-bold text-white">{{ $assignment->incident->title ?? 'Paranormal Emergency' }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                            <span>{{ $assignment->incident->tracking_code ?? '' }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $assignment->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                    <span class="font-mono text-xs px-2.5 py-0.5 rounded
                                        @if($assignment->status === 'accepted') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                        @elseif($assignment->status === 'completed') bg-cyan-500/20 text-cyan-300 border border-cyan-500/30
                                        @elseif($assignment->status === 'declined') bg-red-500/20 text-red-300 border border-red-500/30
                                        @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            @elseif($user->isCitizen())
                {{-- Citizen Reported Incidents --}}
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4 shadow-xl">
                    <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">Citizen Reports</span>
                            <h2 class="text-lg font-bold text-white mt-1">Paranormal Emergency History</h2>
                        </div>
                        <span class="text-xs font-mono text-slate-400">Total: {{ $stats['reported_incidents_count'] }}</span>
                    </div>

                    @if($recentIncidents->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentIncidents as $inc)
                                <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-amber-400 font-semibold">{{ $inc->tracking_code }}</span>
                                            <span class="text-slate-400">&bull;</span>
                                            <span class="font-semibold text-white">{{ $inc->title }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $inc->location_city }} &bull; Reported {{ $inc->created_at->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 shrink-0">
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-mono {{ $inc->threatBadgeClass() }}">{{ $inc->threat_level }}</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-mono {{ $inc->statusBadgeClass() }}">{{ $inc->status }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center text-xs text-slate-500 rounded-xl bg-slate-950 border border-slate-800">
                            This citizen has not submitted any emergency reports yet.
                        </div>
                    @endif
                </div>

            @else
                {{-- Staff Account Information (Admin / Dispatcher) --}}
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4 shadow-xl">
                    <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">HQ Clearance</span>
                    <h2 class="text-lg font-bold text-white">System Operations Clearance</h2>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        This user holds an operational <strong class="text-white uppercase font-mono">{{ $user->role }}</strong> account with direct system dispatch and administrative capabilities.
                    </p>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
