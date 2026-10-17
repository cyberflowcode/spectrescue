@extends('layouts.app')

@section('title', 'Responder Operations')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full {{ $responder->isApproved() ? 'bg-cyan-400 animate-pulse' : 'bg-slate-500' }}"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-cyan-400">Field Responder Operations</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">{{ Auth::user()->name }}</h1>
            <p class="text-sm text-slate-400 mt-0.5 font-mono">
                <span class="text-cyan-400">{{ $responder->badge_number }}</span>
                <span class="text-slate-600 mx-1.5">·</span>
                <span class="text-slate-300">{{ $responder->specialization }}</span>
            </p>
        </div>

        {{-- Readiness Status Badge --}}
        <div class="shrink-0 flex items-center space-x-2">
            @if($responder->isApproved())
                <span class="px-4 py-2 rounded-xl text-xs font-mono font-bold bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Ready for Dispatch</span>
                </span>
            @else
                <span class="px-4 py-2 rounded-xl text-xs font-mono font-bold bg-amber-500/10 text-amber-300 border border-amber-500/30 flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Verification Pending</span>
                </span>
            @endif
        </div>
    </div>

    {{-- Pending Verification Banner --}}
    @if(!$responder->isApproved())
        <div class="p-4 rounded-2xl border flex items-start space-x-4
            {{ $responder->verification_status === 'rejected' ? 'bg-red-500/10 border-red-500/30 text-red-200' : 'bg-amber-500/10 border-amber-500/30 text-amber-200' }}">
            <svg class="w-5 h-5 shrink-0 mt-0.5 {{ $responder->verification_status === 'rejected' ? 'text-red-400' : 'text-amber-400' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-sm space-y-1">
                <p class="font-bold">
                    @if($responder->verification_status === 'rejected')
                        Application Rejected
                    @else
                        Application Under Review
                    @endif
                </p>
                <p class="text-xs opacity-80">
                    @if($responder->verification_status === 'rejected')
                        Your specialist application was not approved. Contact administration for details.
                    @else
                        Your credentials are being reviewed by administrators. Once approved you'll receive emergency dispatch calls.
                    @endif
                </p>
            </div>
        </div>
    @endif

    {{-- Stats --}}
    <div id="responder-stats-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900 border border-cyan-500/20 hover:border-cyan-500/40 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-cyan-400">Active Missions</div>
            <div class="text-3xl font-extrabold font-mono text-cyan-300 mt-2 leading-none">{{ $stats['active_missions'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Requiring action now</div>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900 border border-emerald-500/20 hover:border-emerald-500/40 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-emerald-400">Neutralized</div>
            <div class="text-3xl font-extrabold font-mono text-emerald-300 mt-2 leading-none">{{ $stats['resolved_missions'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Cases successfully closed</div>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900 border border-indigo-500/20 hover:border-indigo-500/40 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-indigo-400">Accreditation</div>
            <div class="text-2xl font-extrabold font-mono mt-2 leading-none
                {{ $responder->isApproved() ? 'text-indigo-300' : 'text-amber-300' }}">
                {{ $responder->isApproved() ? 'CLASS A' : 'PENDING' }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1">Official license status</div>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900 border border-amber-500/20 hover:border-amber-500/40 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-amber-400">Field Readiness</div>
            <div class="text-2xl font-extrabold font-mono mt-2 leading-none
                {{ $responder->isApproved() ? 'text-emerald-300' : 'text-amber-300' }}">
                {{ $responder->isApproved() ? 'ACTIVE' : 'ON HOLD' }}
            </div>
            <div class="text-[11px] text-slate-500 mt-1">{{ $responder->isApproved() ? 'Authorized for field response' : 'Awaiting verification' }}</div>
        </div>
    </div>

    {{-- Active Missions --}}
    <div id="responder-active-missions-block" class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-white">
                Active Missions
                <span class="ml-2 text-sm text-cyan-300 font-mono">({{ $activeAssignments->count() }})</span>
            </h2>
            @if($activeAssignments->count() > 0)
                <span class="text-xs font-mono text-amber-400 animate-pulse">● Immediate Action Required</span>
            @endif
        </div>

        @if($activeAssignments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($activeAssignments as $assignment)
                    @php $incident = $assignment->incident; @endphp
                    <div class="p-5 rounded-2xl bg-slate-900 border border-cyan-500/30 space-y-4 shadow-xl shadow-cyan-950/20 hover:border-cyan-500/50 transition-colors">

                        {{-- Incident Header --}}
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="font-mono text-xs text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
                                </div>
                                <h3 class="font-bold text-white text-base leading-tight">{{ $incident->title }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $incident->incidentType->name ?? 'Unclassified' }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-mono font-bold {{ $incident->threatBadgeClass() }}">{{ $incident->threat_level }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-mono font-bold {{ $incident->statusBadgeClass() }}">{{ $incident->status }}</span>
                            </div>
                        </div>

                        {{-- Location + Caller --}}
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-start space-x-2 text-slate-300">
                                <svg class="w-3.5 h-3.5 text-slate-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span>{{ $incident->location_address }}, {{ $incident->location_city }}</span>
                            </div>
                            <div class="flex items-center space-x-2 text-slate-400">
                                <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>{{ $incident->citizen->name ?? 'Citizen' }}
                                    @if($incident->citizen->phone)
                                        · <span class="font-mono text-slate-300">{{ $incident->citizen->phone }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        {{-- Dispatcher Notes --}}
                        @if($assignment->dispatcher_notes)
                            <div class="p-3 rounded-xl bg-slate-950 border border-amber-500/20 text-xs">
                                <span class="text-amber-400 font-mono font-bold text-[10px] uppercase block mb-1">Dispatcher Directives</span>
                                <p class="text-slate-300 leading-relaxed">{{ $assignment->dispatcher_notes }}</p>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-slate-800">
                            @if($assignment->status === 'assigned')
                                <form method="POST" action="{{ route('responder.assignments.respond', $assignment->id) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="accept">
                                    <input type="hidden" name="redirect_to" value="{{ route('responder.dashboard') }}">
                                    <button type="submit"
                                        class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow shadow-emerald-500/20 transition-all cursor-pointer">
                                        ✓ Accept Mission
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('responder.assignments.respond', $assignment->id) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="decline">
                                    <input type="hidden" name="redirect_to" value="{{ route('responder.dashboard') }}">
                                    <button type="submit"
                                        onclick="return confirm('Decline this emergency call? It returns to the dispatch queue.');"
                                        class="px-3 py-2 rounded-xl text-xs font-medium bg-slate-800 hover:bg-red-500/15 text-slate-400 hover:text-red-300 border border-slate-700 transition-colors cursor-pointer">
                                        Decline
                                    </button>
                                </form>
                            @elseif($assignment->status === 'accepted')
                                <a href="{{ route('responder.reports.create', $incident->id) }}"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow shadow-cyan-500/20 transition-all">
                                    Submit Containment Report →
                                </a>
                            @endif
                            <a href="{{ route('responder.assignments.show', $assignment->id) }}"
                                class="px-3 py-2 rounded-xl text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                                Full Briefing
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-10 rounded-2xl bg-slate-900 border border-slate-800 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-400">No active missions. You're on standby for incoming paranormal alerts.</p>
            </div>
        @endif
    </div>

    {{-- Past Cases --}}
    @if($completedAssignments->count() > 0)
        <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="font-bold text-white text-base">Case History
                    <span class="ml-2 text-sm text-emerald-300 font-mono">({{ $completedAssignments->count() }})</span>
                </h3>
                <span class="text-xs font-mono text-emerald-400">Archived</span>
            </div>
            <div class="divide-y divide-slate-800">
                @foreach($completedAssignments as $past)
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="space-y-0.5">
                            <div class="flex items-center space-x-2 flex-wrap gap-1">
                                <span class="font-mono text-xs text-cyan-400 font-bold">{{ $past->incident->tracking_code }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">Resolved</span>
                            </div>
                            <p class="text-sm font-semibold text-white">{{ $past->incident->title }}</p>
                            <p class="text-xs text-slate-400">
                                Method:
                                <span class="text-emerald-300 font-mono ml-1">{{ $past->incident->responseReport->containment_method ?? 'Neutralized' }}</span>
                            </p>
                        </div>
                        <div class="font-mono text-xs text-slate-500 shrink-0">
                            {{ $past->incident->updated_at->format('M d, Y') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    // Real-time mission dispatch listener for specialists (Zero Tab Reload)
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.channel('user.{{ Auth::id() }}')
                .listen('.NotificationSent', function(event) {
                    handleResponderLiveAlert(event.notification || event);
                })
                .listen('NotificationSent', function(event) {
                    handleResponderLiveAlert(event.notification || event);
                });
        }

        // Silent background refresh: Updates DOM without reloading the tab
        async function refreshResponderSilently() {
            try {
                const response = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) return;
                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                const ids = ['responder-stats-grid', 'responder-active-missions-block'];
                ids.forEach(id => {
                    const newEl = doc.getElementById(id);
                    const curEl = document.getElementById(id);
                    if (newEl && curEl) curEl.innerHTML = newEl.innerHTML;
                });
            } catch (e) {
                console.warn('Responder mission sync notice:', e);
            }
        }

        function handleResponderLiveAlert(notif) {
            if (typeof showLiveToast === 'function') {
                showLiveToast(notif.title || 'Mission Dispatch Alert', notif.message || 'Incoming dispatch directive.', notif.link);
            }
            // Silent DOM update without reloading the tab!
            refreshResponderSilently();
        }

        // Safety fallback: periodic silent check every 30s without reloading the tab
        setInterval(function() {
            if (!document.hidden) {
                refreshResponderSilently();
            }
        }, 30000);
    });
</script>
@endpush
