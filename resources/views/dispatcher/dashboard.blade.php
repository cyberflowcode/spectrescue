@extends('layouts.app')

@section('title', 'Dispatcher Operations Console')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-amber-400">Emergency Dispatch Operations</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Dispatcher Control Console</h1>
            <p class="text-sm text-slate-400 mt-0.5">Triage paranormal calls, assess threat levels, and dispatch field units</p>
        </div>
        <div class="flex items-center space-x-3 shrink-0">
            <div id="ws-radar-status" class="flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-mono font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span id="ws-radar-label">Radar: WebSockets Live</span>
            </div>
            <span class="text-xs font-mono text-slate-400">Duty Officer:</span>
            <span class="text-xs font-mono px-3 py-1.5 rounded-lg bg-amber-500/10 text-amber-300 border border-amber-500/30 font-bold">
                {{ Auth::user()->name }}
            </span>
        </div>
    </div>

    {{-- Live Metric Cards --}}
    <div id="dispatcher-live-metrics" class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900 border border-amber-500/30 shadow-lg shadow-amber-950/20 relative overflow-hidden group hover:border-amber-500/60 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs uppercase tracking-wider font-mono text-amber-400">Pending Triage</span>
                @if($stats['pending_count'] > 0)
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
                @endif
            </div>
            <div class="text-4xl font-extrabold font-mono text-amber-300 leading-none">{{ $stats['pending_count'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1.5">Awaiting specialist assignment</div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-cyan-500/20 hover:border-cyan-500/50 transition-colors">
            <span class="text-xs uppercase tracking-wider font-mono text-cyan-400">Active Missions</span>
            <div class="text-4xl font-extrabold font-mono text-cyan-300 mt-2 leading-none">{{ $stats['active_count'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1.5">Dispatched or responding</div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-red-500/20 hover:border-red-500/50 transition-colors">
            <span class="text-xs uppercase tracking-wider font-mono text-red-400">Critical Alerts</span>
            <div class="text-4xl font-extrabold font-mono text-red-400 mt-2 leading-none">{{ $stats['critical_count'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1.5">High-priority emergencies</div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-indigo-500/20 hover:border-indigo-500/50 transition-colors">
            <span class="text-xs uppercase tracking-wider font-mono text-indigo-400">Available Responders</span>
            <div class="text-4xl font-extrabold font-mono text-indigo-300 mt-2 leading-none">{{ $stats['responders_available'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1.5">Verified units on standby</div>
        </div>
    </div>

    {{-- ===== UPGRADED TACTICAL MAP ===== --}}
    @php
        $mappedIncidents = $pendingIncidents->concat($activeIncidents)->filter(fn($i) => $i->hasCoordinates())->values();
        $incidentsData = $mappedIncidents->map(function($inc) {
            return [
                'id'      => $inc->id,
                'code'    => $inc->tracking_code,
                'title'   => $inc->title,
                'threat'  => $inc->threat_level,
                'status'  => $inc->status,
                'address' => $inc->location_address . ', ' . $inc->location_city,
                'lat'     => (float)$inc->latitude,
                'lng'     => (float)$inc->longitude,
                'url'     => route('dispatcher.incidents.show', $inc->id),
            ];
        });
    @endphp
    <script id="incidents-map-data" type="application/json">@json($incidentsData)</script>

    <div class="rounded-2xl bg-slate-900 border border-cyan-500/20 overflow-hidden shadow-xl shadow-cyan-950/10">
        {{-- Map Header --}}
        <div class="px-5 py-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <span class="w-3 h-3 rounded-full bg-cyan-400 block"></span>
                    <span class="w-3 h-3 rounded-full bg-cyan-400 animate-ping absolute inset-0"></span>
                </div>
                <h2 class="font-bold text-white text-base">Live Tactical Radar &amp; Anomaly Map</h2>
                <span class="text-xs font-mono px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                    {{ $mappedIncidents->count() }} Pinned
                </span>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs font-mono text-slate-400">
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-red-500 border-2 border-red-300"></span>
                    <span class="text-red-300">Critical</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-orange-500 border-2 border-orange-300"></span>
                    <span class="text-orange-300">High</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-yellow-500 border-2 border-yellow-300"></span>
                    <span class="text-yellow-300">Medium</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-emerald-300"></span>
                    <span class="text-emerald-300">Low</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-amber-400 border-2 border-amber-200"></span>
                    <span class="text-amber-300">Pending</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <span class="w-3 h-3 rounded-full bg-cyan-400 border-2 border-cyan-200"></span>
                    <span class="text-cyan-300">Active</span>
                </span>
            </div>
        </div>

        {{-- Map Container --}}
        <div id="tactical-radar-map" class="h-72 sm:h-96 lg:h-[520px] w-full relative z-0"></div>

        {{-- Map Footer --}}
        <div class="px-5 py-2.5 border-t border-slate-800 flex items-center justify-between text-[11px] font-mono text-slate-500">
            <span>• Real-time GPS mapping • Click any marker for mission triage • Scroll to zoom</span>
            <span class="text-cyan-400">OpenStreetMap · Free · No API Key</span>
        </div>
    </div>

    {{-- ===== PENDING EMERGENCIES QUEUE ===== --}}
    <div id="dispatcher-pending-queue" class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 bg-amber-950/10 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                <h2 class="font-bold text-white text-base">
                    Pending Calls Requiring Dispatch
                    <span class="ml-2 text-amber-300">({{ $pendingIncidents->count() }})</span>
                </h2>
            </div>
            <span class="text-xs font-mono text-amber-400">Immediate Action Required</span>
        </div>

        @if($pendingIncidents->count() > 0)
            {{-- Desktop table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-mono text-xs uppercase">
                            <th class="py-3.5 px-6">Code</th>
                            <th class="py-3.5 px-6">Incident &amp; Type</th>
                            <th class="py-3.5 px-6">Caller</th>
                            <th class="py-3.5 px-6">Location</th>
                            <th class="py-3.5 px-6">Threat</th>
                            <th class="py-3.5 px-6">Logged</th>
                            <th class="py-3.5 px-6 text-right">Dispatch</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @foreach($pendingIncidents as $incident)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 font-mono text-xs font-bold text-amber-400">{{ $incident->tracking_code }}</td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-white">{{ $incident->title }}</div>
                                <span class="text-xs text-slate-400">{{ $incident->incidentType->name ?? 'Unclassified' }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs">
                                <div class="text-slate-200">{{ $incident->citizen->name ?? 'Citizen' }}</div>
                                <div class="text-slate-500 font-mono text-[10px]">{{ $incident->citizen->phone ?? 'No Phone' }}</div>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-300">
                                <div>{{ $incident->location_address }}</div>
                                <div class="text-slate-500 text-[10px]">{{ $incident->location_city }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $incident->threatBadgeClass() }}">
                                    {{ $incident->threat_level }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs font-mono text-slate-400">
                                {{ $incident->created_at->diffForHumans() }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('dispatcher.incidents.show', $incident->id) }}"
                                    class="px-4 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-md shadow-amber-500/20 transition-all inline-flex items-center space-x-1">
                                    <span>Dispatch Unit</span>
                                    <span>→</span>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Mobile cards --}}
            <div class="md:hidden divide-y divide-slate-800">
                @foreach($pendingIncidents as $incident)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-mono text-xs text-amber-400 font-bold">{{ $incident->tracking_code }}</span>
                            <div class="font-semibold text-white mt-0.5">{{ $incident->title }}</div>
                            <div class="text-xs text-slate-400">{{ $incident->location_city }} · {{ $incident->created_at->diffForHumans() }}</div>
                        </div>
                        <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->threatBadgeClass() }}">{{ $incident->threat_level }}</span>
                    </div>
                    <a href="{{ route('dispatcher.incidents.show', $incident->id) }}"
                        class="block w-full text-center py-2.5 rounded-xl text-xs font-bold bg-amber-500 text-slate-950">
                        Dispatch Unit →
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-10 text-center text-xs text-slate-400">
                ✓ No pending calls in queue. All active emergencies have been dispatched.
            </div>
        @endif
    </div>

    {{-- ===== ACTIVE MISSIONS ===== --}}
    <div id="dispatcher-active-queue" class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h2 class="font-bold text-white text-base">
                Active Field Operations
                <span class="ml-2 text-cyan-300">({{ $activeIncidents->count() }})</span>
            </h2>
            <span class="text-xs font-mono text-cyan-400">Live Mission Monitoring</span>
        </div>

        @if($activeIncidents->count() > 0)
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-950/60 border-b border-slate-800 text-slate-400 font-mono text-xs uppercase">
                            <th class="py-3.5 px-6">Code</th>
                            <th class="py-3.5 px-6">Emergency</th>
                            <th class="py-3.5 px-6">Location</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Assigned Specialist</th>
                            <th class="py-3.5 px-6">Dispatched</th>
                            <th class="py-3.5 px-6 text-right">Monitor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200">
                        @foreach($activeIncidents as $incident)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-4 px-6 font-mono text-xs font-bold text-cyan-400">{{ $incident->tracking_code }}</td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-white">{{ $incident->title }}</div>
                                <span class="text-xs text-slate-400">{{ $incident->incidentType->name ?? 'Unclassified' }}</span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-300">
                                {{ $incident->location_address }}, {{ $incident->location_city }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-mono font-bold {{ $incident->statusBadgeClass() }}">
                                    {{ $incident->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs">
                                @if($incident->latestAssignment && $incident->latestAssignment->responder)
                                    <div class="font-bold text-slate-200">{{ $incident->latestAssignment->responder->user->name }}</div>
                                    <span class="text-cyan-400 font-mono text-[10px]">
                                        {{ $incident->latestAssignment->responder->badge_number }} ({{ $incident->latestAssignment->responder->specialization }})
                                    </span>
                                @else
                                    <span class="text-slate-500 italic">None</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs font-mono text-slate-400">
                                {{ $incident->latestAssignment ? $incident->latestAssignment->created_at->diffForHumans() : '—' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('dispatcher.incidents.show', $incident->id) }}"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-800 hover:bg-slate-700 text-cyan-400 border border-slate-700 transition-colors">
                                    Monitor →
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Mobile --}}
            <div class="md:hidden divide-y divide-slate-800">
                @foreach($activeIncidents as $incident)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-mono text-xs text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
                            <div class="font-semibold text-white mt-0.5">{{ $incident->title }}</div>
                            @if($incident->latestAssignment && $incident->latestAssignment->responder)
                                <div class="text-xs text-slate-400 mt-0.5">→ {{ $incident->latestAssignment->responder->user->name }}</div>
                            @endif
                        </div>
                        <span class="shrink-0 text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->statusBadgeClass() }}">{{ $incident->status }}</span>
                    </div>
                    <a href="{{ route('dispatcher.incidents.show', $incident->id) }}"
                        class="block w-full text-center py-2 rounded-xl text-xs font-medium bg-slate-800 text-cyan-400 border border-slate-700">
                        Monitor →
                    </a>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-10 text-center text-xs text-slate-400">
                No missions currently active in the field.
            </div>
        @endif
    </div>

    {{-- ===== AVAILABLE SPECIALISTS ===== --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
        <h3 class="font-bold text-white text-base">
            Standby Field Specialists
            <span class="ml-2 text-emerald-300 font-mono text-sm">({{ $availableResponders->count() }})</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($availableResponders as $resp)
                <div class="p-4 rounded-xl bg-slate-950 border border-emerald-500/20 space-y-2 hover:border-emerald-500/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white text-sm">{{ $resp->user->name }}</span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span>Ready</span>
                        </span>
                    </div>
                    <div class="text-xs text-cyan-400 font-medium">{{ $resp->specialization }}</div>
                    <div class="text-xs font-mono text-slate-400">{{ $resp->badge_number }}</div>
                    @if($resp->bio)
                        <div class="text-[10px] text-slate-500 line-clamp-2">{{ $resp->bio }}</div>
                    @endif
                </div>
            @empty
                <div class="col-span-full p-8 text-center text-xs text-slate-500 italic">
                    No verified responders are currently available. Check Admin verification queue.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    /*
     * Dark map effect: We use the free OpenStreetMap tiles (no API key needed)
     * and apply a CSS invert + hue-rotate filter to turn the light map dark.
     * This is a well-known technique — no external service required.
     */
    .leaflet-tile-pane {
        filter: invert(1) hue-rotate(180deg) brightness(0.85) saturate(0.9) contrast(0.95);
    }
    /* Keep markers and popups looking normal (un-invert them) */
    .leaflet-marker-pane,
    .leaflet-popup-pane,
    .leaflet-shadow-pane,
    .leaflet-overlay-pane {
        filter: invert(1) hue-rotate(180deg);
    }

    /* Dark popup styling */
    .leaflet-popup-content-wrapper {
        background: #0f172a !important;
        color: #f1f5f9 !important;
        border: 1px solid #334155 !important;
        border-radius: 0.85rem !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.5) !important;
        font-family: 'Inter', sans-serif !important;
    }
    .leaflet-popup-tip { background: #0f172a !important; }
    .leaflet-popup-close-button { color: #64748b !important; }
    .leaflet-popup-close-button:hover { color: #f1f5f9 !important; }

    /* Critical pulse ring */
    @keyframes critical-pulse {
        0%   { transform: scale(1);    opacity: 0.7; }
        70%  { transform: scale(2.2);  opacity: 0; }
        100% { transform: scale(1);    opacity: 0; }
    }
    .critical-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        animation: critical-pulse 1.8s ease-out infinite;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const incidents = {!! json_encode($incidentsData) !!};

    // Center on Philippines / first incident
    const defaultLat = incidents.length > 0 ? incidents[0].lat : 14.5995;
    const defaultLng = incidents.length > 0 ? incidents[0].lng : 120.9842;

    const map = L.map('tactical-radar-map', {
        zoomControl: true,
        scrollWheelZoom: true,   // Allow scroll zoom on desktop
        doubleClickZoom: true,
    }).setView([defaultLat, defaultLng], 12);

    // 🗺️ FREE MAP TILES — OpenStreetMap (no API key, no account needed)
    // The dark look is achieved via a CSS filter on .leaflet-tile-pane
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Color scheme by threat level
    const threatColors = {
        'Critical': { fill: '#ef4444', ring: '#fca5a5', border: '#fca5a5' },
        'High':     { fill: '#f97316', ring: '#fdba74', border: '#fdba74' },
        'Medium':   { fill: '#eab308', ring: '#fde047', border: '#fde047' },
        'Low':      { fill: '#22c55e', ring: '#86efac', border: '#86efac' },
    };

    // Status overlay ring color
    const statusColors = {
        'Pending':    '#fbbf24',
        'Dispatched': '#22d3ee',
        'Responding': '#c084fc',
    };

    // Layer group for dynamic, seamless marker updates
    const markersLayer = L.layerGroup().addTo(map);

    function renderMarkers(items, fit = false) {
        markersLayer.clearLayers();
        if (!items || items.length === 0) return;
        const bounds = [];

        items.forEach(inc => {
            const colors = threatColors[inc.threat] || { fill: '#94a3b8', ring: '#cbd5e1', border: '#cbd5e1' };
            const statusColor = statusColors[inc.status] || '#94a3b8';
            const isCritical = inc.threat === 'Critical';

            const iconHtml = `
                <div style="position: relative; width: 28px; height: 28px;">
                    ${isCritical ? `<div class="critical-ring" style="background: ${colors.fill}; opacity: 0.5;"></div>` : ''}
                    <div style="position: absolute; inset: 4px; border-radius: 50%; background: ${colors.fill}; border: 2.5px solid ${colors.border}; box-shadow: 0 0 10px ${colors.fill}60; display: flex; align-items: center; justify-content: center;">
                        <div style="width: 6px; height: 6px; border-radius: 50%; background: white; opacity: 0.9;"></div>
                    </div>
                    <div style="position: absolute; bottom: -2px; right: -2px; width: 10px; height: 10px; border-radius: 50%; background: ${statusColor}; border: 1.5px solid #0f172a;"></div>
                </div>
            `;

            const icon = L.divIcon({
                html: iconHtml,
                className: '',
                iconSize: [28, 28],
                iconAnchor: [14, 14],
                popupAnchor: [0, -16],
            });

            const marker = L.marker([inc.lat, inc.lng], { icon });
            markersLayer.addLayer(marker);
            bounds.push([inc.lat, inc.lng]);

            marker.bindPopup(`
                <div style="min-width: 200px; padding: 4px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: ${statusColor}; font-weight: 800; font-family: 'JetBrains Mono', monospace; font-size: 11px; letter-spacing: 0.05em;">
                            ${inc.code}
                        </span>
                        <span style="background: ${colors.fill}30; color: ${colors.border}; border: 1px solid ${colors.fill}50; padding: 1px 7px; border-radius: 999px; font-size: 9px; font-weight: 700; font-family: monospace; text-transform: uppercase;">${inc.threat}</span>
                    </div>
                    <div style="font-weight: 700; color: #f1f5f9; font-size: 13px; margin-bottom: 4px; line-height: 1.3;">${inc.title}</div>
                    <div style="color: #94a3b8; font-size: 11px; margin-bottom: 10px;">📍 ${inc.address}</div>
                    <div style="display: inline-flex; align-items: center; background: ${statusColor}20; color: ${statusColor}; border: 1px solid ${statusColor}50; border-radius: 999px; padding: 2px 8px; font-size: 9px; font-weight: 600; font-family: monospace; text-transform: uppercase; margin-bottom: 10px;">⚡ ${inc.status}</div>
                    <a href="${inc.url}" style="display: block; text-align: center; background: #0284c7; color: #ffffff; font-size: 11px; font-weight: 700; padding: 7px 10px; border-radius: 8px; text-decoration: none; letter-spacing: 0.03em;">Open Dispatch Console →</a>
                </div>
            `);

            marker.on('dblclick', function() {
                window.location.href = inc.url;
            });
        });

        if (fit && bounds.length > 0) {
            if (bounds.length === 1) {
                map.setView(bounds[0], 14);
            } else {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }
    }

    // Initial map render
    renderMarkers(incidents, true);

    // ==================== DISPATCHER REAL-TIME WEBSOCKET LISTENER (ZERO TAB RELOAD) ====================
    if (window.Echo) {
        window.Echo.channel('emergencies')
            .listen('.NewEmergencyReported', function(event) {
                handleIncomingLiveEmergency(event.incident || event, 'New Emergency');
            })
            .listen('NewEmergencyReported', function(event) {
                handleIncomingLiveEmergency(event.incident || event, 'New Emergency');
            })
            .listen('.IncidentStatusUpdated', function(event) {
                handleIncomingLiveEmergency(event.incident || event, 'Status Changed');
            })
            .listen('IncidentStatusUpdated', function(event) {
                handleIncomingLiveEmergency(event.incident || event, 'Status Changed');
            });
    } else {
        const radarLabel = document.getElementById('ws-radar-label');
        if (radarLabel) radarLabel.innerText = 'Radar: Polling (Fallback)';
    }

    // Silent background refresh: Updates DOM without reloading the tab
    async function refreshDispatcherContentSilently() {
        try {
            const response = await fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) return;
            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');

            // Swap metric cards seamlessly
            const newMetrics = doc.getElementById('dispatcher-live-metrics');
            const curMetrics = document.getElementById('dispatcher-live-metrics');
            if (newMetrics && curMetrics) curMetrics.innerHTML = newMetrics.innerHTML;

            // Swap pending emergencies queue seamlessly
            const newPending = doc.getElementById('dispatcher-pending-queue');
            const curPending = document.getElementById('dispatcher-pending-queue');
            if (newPending && curPending) curPending.innerHTML = newPending.innerHTML;

            // Swap active missions queue seamlessly
            const newActive = doc.getElementById('dispatcher-active-queue');
            const curActive = document.getElementById('dispatcher-active-queue');
            if (newActive && curActive) curActive.innerHTML = newActive.innerHTML;

            // Update map markers smoothly
            const newMapScript = doc.getElementById('incidents-map-data');
            if (newMapScript) {
                try {
                    const newData = JSON.parse(newMapScript.textContent);
                    renderMarkers(newData, false);
                } catch (e) {}
            }

            // Return radar pill to steady state
            const radarPill = document.getElementById('ws-radar-status');
            if (radarPill) {
                radarPill.classList.remove('bg-amber-500/20', 'border-amber-500/50', 'text-amber-300');
                radarPill.classList.add('bg-emerald-500/10', 'border-emerald-500/30', 'text-emerald-400');
                const label = document.getElementById('ws-radar-label');
                if (label) label.innerText = 'Radar: WebSockets Live';
            }
        } catch (err) {
            console.warn('Dispatcher live sync notice:', err);
        }
    }

    function handleIncomingLiveEmergency(incident, type = 'New Emergency') {
        // Sound priority dispatch chime
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(520, ctx.currentTime);
                osc.frequency.setValueAtTime(780, ctx.currentTime + 0.12);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            }
        } catch (e) {}

        // Pulse radar pill during live sync
        const radarPill = document.getElementById('ws-radar-status');
        if (radarPill) {
            radarPill.classList.remove('bg-emerald-500/10', 'border-emerald-500/30', 'text-emerald-400');
            radarPill.classList.add('bg-amber-500/20', 'border-amber-500/50', 'text-amber-300');
            const label = document.getElementById('ws-radar-label');
            if (label) label.innerText = 'Syncing Live Feed...';
        }

        // Silent DOM update without reloading the tab!
        refreshDispatcherContentSilently();
    }

    // Safety fallback: periodic silent check every 30s without reloading the tab
    setInterval(function() {
        if (!document.hidden) {
            refreshDispatcherContentSilently();
        }
    }, 30000);
});
</script>
@endpush
