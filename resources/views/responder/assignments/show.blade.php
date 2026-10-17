@extends('layouts.app')

@section('title', 'Mission Briefing: ' . $assignment->incident->tracking_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('responder.dashboard') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1">
            <span>&larr; Back to Missions Dashboard</span>
        </a>
        <div class="flex items-center space-x-2">
            <span class="text-xs px-2.5 py-0.5 rounded-full font-mono font-semibold {{ $assignment->incident->threatBadgeClass() }}">
                Threat: {{ $assignment->incident->threat_level }}
            </span>
            <span class="text-xs px-2.5 py-0.5 rounded-full font-mono font-semibold {{ $assignment->incident->statusBadgeClass() }}">
                Status: {{ $assignment->incident->status }}
            </span>
        </div>
    </div>

    <!-- Mission Briefing Card -->
    <div class="rounded-2xl bg-slate-900 border border-cyan-500/30 p-6 sm:p-8 space-y-6 shadow-xl shadow-cyan-950/20">
        
        <div class="border-b border-slate-800 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-mono text-cyan-400 font-bold uppercase tracking-wider">Mission Briefing &bull; {{ $assignment->incident->tracking_code }}</span>
                <h1 class="text-2xl font-bold text-white mt-1">{{ $assignment->incident->title }}</h1>
                <div class="text-xs text-slate-400 mt-1 font-mono">
                    Dispatched by {{ $assignment->dispatcher->name ?? 'Headquarters' }} on {{ $assignment->created_at->format('M d, Y h:i A') }}
                </div>
            </div>
            <div class="text-sm font-mono text-right">
                <span class="text-slate-500 text-xs uppercase block">Classification</span>
                <span class="text-white font-bold">{{ $assignment->incident->incidentType->name ?? 'Unspecified' }}</span>
            </div>
        </div>

        <!-- Target Location & Caller -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-mono uppercase text-[10px] block">Target Site Address</span>
                    @if($assignment->incident->hasCoordinates())
                        <div class="flex items-center space-x-2 text-[11px] font-mono">
                            <a href="https://www.openstreetmap.org/?mlat={{ $assignment->incident->latitude }}&mlon={{ $assignment->incident->longitude }}#map=16/{{ $assignment->incident->latitude }}/{{ $assignment->incident->longitude }}" target="_blank"
                               class="text-cyan-400 hover:text-cyan-300">
                                OpenStreetMap &rarr;
                            </a>
                            <span class="text-slate-600">&bull;</span>
                            <a href="https://www.google.com/maps?q={{ $assignment->incident->latitude }},{{ $assignment->incident->longitude }}" target="_blank"
                               class="text-slate-400 hover:text-slate-300">
                                Turn-by-Turn GPS
                            </a>
                        </div>
                    @endif
                </div>
                <div class="text-sm font-bold text-white">{{ $assignment->incident->location_address }}</div>
                <div class="text-slate-400">{{ $assignment->incident->location_city }}</div>

                @if($assignment->incident->hasCoordinates())
                    <div class="text-[10px] text-cyan-400 font-mono pt-1">Coordinates: {{ number_format($assignment->incident->latitude, 4) }}, {{ number_format($assignment->incident->longitude, 4) }}</div>
                    <div id="responder-mission-map" class="h-40 w-full rounded-lg overflow-hidden border border-slate-800 mt-2 relative z-0"></div>
                @endif
            </div>

            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                <span class="text-slate-500 font-mono uppercase text-[10px] block">Reporting Witness Contact</span>
                <div class="text-sm font-bold text-white">{{ $assignment->incident->citizen->name ?? 'Citizen' }}</div>
                <div class="text-cyan-400 font-mono">{{ $assignment->incident->citizen->phone ?? 'No phone logged' }}</div>
            </div>
        </div>

        <!-- Dispatcher Tactical Directive -->
        @if($assignment->dispatcher_notes)
            <div class="p-4 rounded-xl bg-amber-950/20 border border-amber-500/30 text-xs space-y-1">
                <span class="text-amber-400 font-mono font-bold uppercase text-[10px] block">&bull; Dispatcher Operational Directive</span>
                <p class="text-slate-200 leading-relaxed">{{ $assignment->dispatcher_notes }}</p>
            </div>
        @endif

        <!-- Phenomenon Narrative -->
        <div class="space-y-2">
            <span class="text-xs font-mono uppercase text-slate-400">Witness Description of Phenomenon</span>
            <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-200 leading-relaxed whitespace-pre-line">
                {{ $assignment->incident->description }}
            </div>
        </div>

        <!-- Evidence Photos -->
        @if($assignment->incident->evidence->count() > 0)
            <div class="space-y-3 pt-2">
                <span class="text-xs font-mono uppercase text-slate-400">Field Evidence Photos</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($assignment->incident->evidence as $evidence)
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                            @if(in_array(pathinfo($evidence->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp']))
                                <img src="{{ asset('storage/' . $evidence->file_path) }}" alt="Evidence" class="w-full h-48 object-cover rounded-lg border border-slate-800">
                            @else
                                <div class="h-32 flex items-center justify-center bg-slate-900 rounded-lg text-xs font-mono text-cyan-400 border border-slate-800">
                                    [Evidence Artifact]
                                </div>
                            @endif
                            <div class="text-xs text-slate-300 font-medium">{{ $evidence->caption ?? 'Evidence File' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Resolution Report Dossier (When Resolved / Completed) -->
        @if($assignment->incident->responseReport)
            @php $report = $assignment->incident->responseReport; @endphp
            <div class="p-5 rounded-xl bg-emerald-950/20 border border-emerald-500/30 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-emerald-500/20 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        <h3 class="text-sm font-bold text-white uppercase tracking-wider font-mono">Official Containment Dossier</h3>
                    </div>
                    <span class="text-xs font-mono text-emerald-400">
                        Filed: {{ $report->submitted_at ? $report->submitted_at->format('M d, Y - h:i A') : $report->created_at->format('M d, Y - h:i A') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="space-y-1">
                        <span class="text-[10px] uppercase font-mono text-slate-500 block">Containment Protocol Executed</span>
                        <div class="text-sm font-bold text-emerald-300 font-mono">{{ $report->containment_method }}</div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] uppercase font-mono text-slate-500 block">Entity Neutralization</span>
                        @if($report->threat_neutralized)
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                ✓ Threat Fully Neutralized
                            </span>
                        @else
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                ⚠ Entity Contained (Unneutralized)
                            </span>
                        @endif
                    </div>
                </div>

                <div class="space-y-1 text-xs">
                    <span class="text-[10px] uppercase font-mono text-slate-500 block">Field Findings & Summary</span>
                    <div class="p-3.5 rounded-lg bg-slate-950/80 border border-slate-800 text-slate-200 leading-relaxed whitespace-pre-line">
                        {{ $report->findings_summary }}
                    </div>
                </div>

                @if($report->evidence_collected)
                    <div class="space-y-1 text-xs">
                        <span class="text-[10px] uppercase font-mono text-slate-500 block">Evidence & Artifacts Collected</span>
                        <div class="p-3.5 rounded-lg bg-slate-950/80 border border-slate-800 text-slate-200 leading-relaxed whitespace-pre-line">
                            {{ $report->evidence_collected }}
                        </div>
                    </div>
                @endif

                @if($report->recommendations)
                    <div class="space-y-1 text-xs">
                        <span class="text-[10px] uppercase font-mono text-slate-500 block">Post-Containment Directives & Recommendations</span>
                        <div class="p-3.5 rounded-lg bg-slate-950/80 border border-slate-800 text-slate-200 leading-relaxed whitespace-pre-line">
                            {{ $report->recommendations }}
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Action Station -->
        <div class="pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
            @if($assignment->incident->status === 'Resolved' || $assignment->incident->responseReport || $assignment->status === 'completed')
                <div class="flex items-center space-x-2 text-xs font-mono text-emerald-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>&check; Emergency Mission Completed &bull; Incident Officially Resolved</span>
                </div>
                <a href="{{ route('responder.dashboard') }}"
                    class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors">
                    Back to Missions Dashboard &rarr;
                </a>
            @elseif($assignment->status === 'assigned')
                <div class="flex items-center space-x-3">
                    <!-- Accept Form -->
                    <form method="POST" action="{{ route('responder.assignments.respond', $assignment->id) }}">
                        @csrf
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="redirect_to" value="{{ route('responder.assignments.show', $assignment->id) }}">
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-md shadow-emerald-500/20 text-xs transition-all flex items-center space-x-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Accept Emergency Assignment</span>
                        </button>
                    </form>

                    <!-- Decline Form -->
                    <form method="POST" action="{{ route('responder.assignments.respond', $assignment->id) }}">
                        @csrf
                        <input type="hidden" name="action" value="decline">
                        <input type="hidden" name="redirect_to" value="{{ route('responder.dashboard') }}">
                        <button type="submit" onclick="return confirm('Confirm declining this emergency?');"
                            class="px-4 py-2.5 rounded-xl font-medium bg-slate-800 hover:bg-red-500/20 text-slate-400 hover:text-red-300 border border-slate-700 text-xs transition-colors cursor-pointer">
                            Decline Call
                        </button>
                    </form>
                </div>
            @elseif($assignment->status === 'accepted')
                <div class="flex items-center space-x-3">
                    <span class="text-xs font-mono text-emerald-400 font-semibold">&check; Mission Accepted &bull; In Response</span>
                    <a href="{{ route('responder.reports.create', $assignment->incident->id) }}" class="px-5 py-2.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-md shadow-cyan-500/20 text-xs transition-all">
                        Submit Response Report & Close Incident &rarr;
                    </a>
                </div>
            @elseif($assignment->status === 'declined')
                <div class="flex items-center space-x-2 text-xs font-mono text-slate-500">
                    <span>Assignment Declined</span>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection

@if($assignment->incident->hasCoordinates())
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-popup-content-wrapper {
        background: #0f172a !important;
        color: #f1f5f9 !important;
        border: 1px solid #334155 !important;
        border-radius: 0.75rem !important;
        font-family: 'Instrument Sans', sans-serif !important;
    }
    .leaflet-popup-tip {
        background: #0f172a !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    function initResponderMissionMap() {
        const container = document.getElementById('responder-mission-map');
        if (!container || typeof L === 'undefined') return;

        if (container._leaflet_id) {
            try {
                container._leaflet_id = null;
            } catch(e) {}
        }

        const lat = {{ $assignment->incident->latitude }};
        const lng = {{ $assignment->incident->longitude }};

        try {
            const map = L.map(container, {
                zoomControl: false,
                scrollWheelZoom: false
            }).setView([lat, lng], 15);

            // 100% Free Official OpenStreetMap Tile Server
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
            }).addTo(map);

            const marker = L.marker([lat, lng]).addTo(map);
            marker.bindPopup(`
                <div class="text-xs font-mono font-bold text-cyan-400">{{ $assignment->incident->tracking_code }}</div>
                <div class="text-xs text-white font-bold">{{ $assignment->incident->title }}</div>
                <div class="text-[10px] text-slate-400 mt-1">{{ $assignment->incident->location_address }}</div>
            `).openPopup();
        } catch(e) {
            console.warn('Map initialization:', e);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initResponderMissionMap);
    } else {
        initResponderMissionMap();
    }
    document.addEventListener('spa:navigated', initResponderMissionMap);
</script>
@endpush
@endif
