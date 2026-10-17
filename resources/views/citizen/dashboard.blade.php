@extends('layouts.app')

@section('title', 'Citizen Incident Hub')

@section('content')
<div class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-cyan-400">Citizen Command Hub</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">My Paranormal Reports</h1>
            <p class="text-sm text-slate-400 mt-0.5">Track real-time status of your emergency calls and responder dispatches</p>
        </div>

        <!-- <a href="{{ route('citizen.incidents.create') }}"
            class="px-5 py-3 rounded-xl font-bold bg-red-600 hover:bg-red-500 text-white shadow-lg shadow-red-600/30
            transition-all flex items-center justify-center space-x-2 shrink-0 hover:-translate-y-0.5 transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Report New Emergency</span>
        </a> -->
    </div>

    {{-- Quick Stats Cards --}}
    <div id="citizen-stats-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-slate-400">Total Submitted</div>
            <div class="text-3xl font-extrabold font-mono text-white mt-1.5 leading-none">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">All reports filed</div>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900 border border-amber-500/20 hover:border-amber-500/50 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-amber-400">Pending Review</div>
            <div class="text-3xl font-extrabold font-mono text-amber-300 mt-1.5 leading-none">{{ $stats['pending'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Awaiting dispatch</div>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900 border border-cyan-500/20 hover:border-cyan-500/50 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-cyan-400">Active / Dispatched</div>
            <div class="text-3xl font-extrabold font-mono text-cyan-300 mt-1.5 leading-none">{{ $stats['dispatched'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">In the field now</div>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900 border border-emerald-500/20 hover:border-emerald-500/50 transition-colors">
            <div class="text-xs uppercase tracking-wider font-mono text-emerald-400">Resolved &amp; Safe</div>
            <div class="text-3xl font-extrabold font-mono text-emerald-300 mt-1.5 leading-none">{{ $stats['resolved'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Cases closed</div>
        </div>
    </div>

    {{-- Incidents Table (Desktop) / Cards (Mobile) --}}
    <div id="citizen-logs-card" class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="font-bold text-white text-base">Emergency Logs ({{ $incidents->total() }})</h2>
            <span class="text-xs font-mono text-slate-500">Live Status Feed</span>
        </div>

        @if($incidents->count() > 0)
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-950/60 border-b border-slate-800 text-slate-400 font-mono text-xs uppercase">
                            <th class="py-3.5 px-6">Tracking Code</th>
                            <th class="py-3.5 px-6">Incident</th>
                            <th class="py-3.5 px-6">Type &amp; Location</th>
                            <th class="py-3.5 px-6">Threat</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Assigned Specialist</th>
                            <th class="py-3.5 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @foreach($incidents as $incident)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 font-mono text-xs font-bold text-cyan-400 align-middle">
                                {{ $incident->tracking_code }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-white align-middle">
                                {{ $incident->title }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400 align-middle">
                                <span class="text-slate-200 block font-medium">{{ $incident->incidentType->name ?? 'Unspecified' }}</span>
                                <div class="flex items-center space-x-1.5 mt-0.5">
                                    <span>{{ $incident->location_city }}</span>
                                    <span class="text-slate-600">&bull;</span>
                                    @if($incident->hasCoordinates())
                                        <span class="text-emerald-400 font-mono text-[10px]">Pinned</span>
                                    @else
                                        <span class="text-amber-400 font-mono text-[10px]">No Pin</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 align-middle">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $incident->threatBadgeClass() }}">
                                    {{ $incident->threat_level }}
                                </span>
                            </td>
                            <td class="py-4 px-6 align-middle">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $incident->statusBadgeClass() }}">
                                    {{ $incident->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs align-middle">
                                @if($incident->latestAssignment && $incident->latestAssignment->responder)
                                    <div class="font-medium text-slate-200">{{ $incident->latestAssignment->responder->user->name }}</div>
                                    <span class="text-slate-400 font-mono text-[10px]">{{ $incident->latestAssignment->responder->specialization }}</span>
                                @else
                                    <span class="text-slate-500 italic">Awaiting Dispatcher</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right align-middle whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-2">
                                    @if($incident->status === 'Pending')
                                        <a href="{{ route('citizen.incidents.edit', $incident->id) }}"
                                            class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition-colors"
                                            title="Edit report and coordinates">
                                            Edit
                                        </a>
                                    @endif
                                    <a href="{{ route('citizen.incidents.show', $incident->id) }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800 hover:bg-slate-700 text-cyan-400 hover:text-cyan-300 border border-slate-700 transition-colors">
                                        Track →
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-slate-800">
                @foreach($incidents as $incident)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div class="space-y-0.5">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="font-mono text-xs font-bold text-cyan-400">{{ $incident->tracking_code }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->threatBadgeClass() }}">{{ $incident->threat_level }}</span>
                            </div>
                            <div class="font-semibold text-white text-sm">{{ $incident->title }}</div>
                            <div class="text-xs text-slate-400">{{ $incident->incidentType->name ?? 'Unspecified' }} · {{ $incident->location_city }}</div>
                        </div>
                        <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->statusBadgeClass() }}">
                            {{ $incident->status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-slate-400">
                            @if($incident->latestAssignment && $incident->latestAssignment->responder)
                                <span class="text-slate-300">→ {{ $incident->latestAssignment->responder->user->name }}</span>
                            @else
                                <span class="italic text-slate-600">Awaiting Dispatcher</span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-1.5">
                            @if($incident->status === 'Pending')
                                <a href="{{ route('citizen.incidents.edit', $incident->id) }}"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                    Edit
                                </a>
                            @endif
                            <a href="{{ route('citizen.incidents.show', $incident->id) }}"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800 text-cyan-400 border border-slate-700">
                                Track →
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($incidents->hasPages())
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $incidents->links() }}
                </div>
            @endif
        @else
            <div class="p-14 text-center space-y-4">
                <div class="w-14 h-14 rounded-full bg-slate-800 border border-slate-700 text-slate-500 flex items-center justify-center mx-auto">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-white font-bold text-base">No Emergencies Reported Yet</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1 leading-relaxed">
                        If you are witnessing unexplained poltergeist activity, haunting, or spectral disturbances — file a report immediately.
                    </p>
                </div>
                <a href="{{ route('citizen.incidents.create') }}"
                    class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white text-sm font-bold transition-colors shadow-lg shadow-red-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Report First Emergency</span>
                </a>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Real-time status update for citizen reports (Zero Tab Reload)
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.channel('user.{{ Auth::id() }}')
                .listen('.NotificationSent', function() {
                    refreshCitizenDashboardSilently();
                })
                .listen('NotificationSent', function() {
                    refreshCitizenDashboardSilently();
                });

            window.Echo.channel('emergencies')
                .listen('.IncidentStatusUpdated', function() {
                    refreshCitizenDashboardSilently();
                })
                .listen('IncidentStatusUpdated', function() {
                    refreshCitizenDashboardSilently();
                });
        }

        // Silent background refresh: Updates DOM without reloading the tab
        async function refreshCitizenDashboardSilently() {
            try {
                const response = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) return;
                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                const ids = ['citizen-stats-grid', 'citizen-logs-card'];
                ids.forEach(id => {
                    const newEl = doc.getElementById(id);
                    const curEl = document.getElementById(id);
                    if (newEl && curEl) curEl.innerHTML = newEl.innerHTML;
                });
            } catch (e) {
                console.warn('Citizen dashboard sync notice:', e);
            }
        }

        // Safety fallback: periodic silent check every 30s without reloading the tab
        setInterval(function() {
            if (!document.hidden) {
                refreshCitizenDashboardSilently();
            }
        }, 30000);
    });
</script>
@endpush
