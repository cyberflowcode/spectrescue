@extends('layouts.app')

@section('title', 'Emergency Status: ' . $incident->tracking_code)

@section('content')
<div class="w-full space-y-6">

    {{-- 1. Navigation Breadcrumb & Quick Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-2">
            <a href="{{ route('citizen.dashboard') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
                <span>&larr; Back to Incident Log</span>
            </a>
            <span class="text-slate-600">/</span>
            <span class="text-xs font-mono text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
        </div>
        <div id="citizen-header-actions" class="flex flex-wrap items-center gap-2">
            @if($incident->status === 'Pending')
                <a href="{{ route('citizen.incidents.edit', $incident->id) }}"
                    class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold font-mono bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/40 transition-all shadow-sm shadow-cyan-950/20 hover:-translate-y-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit Report &amp; Map Pin</span>
                </a>
            @endif
            <span class="text-xs px-2.5 py-1 rounded-full font-mono font-semibold {{ $incident->threatBadgeClass() }}">
                Threat: {{ $incident->threat_level }}
            </span>
            <span class="text-xs px-2.5 py-1 rounded-full font-mono font-semibold {{ $incident->statusBadgeClass() }}">
                {{ $incident->status }}
            </span>
        </div>
    </div>

    {{-- 2. Header Banner & Progress Pipeline --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-7 shadow-xl shadow-slate-950/40 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800/80 pb-5">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span class="text-xs font-mono uppercase tracking-widest text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
                    <span class="text-slate-600">&bull;</span>
                    <span class="text-xs font-mono text-slate-400">{{ $incident->incidentType->name ?? 'Unclassified' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $incident->title }}</h1>
                <p class="text-xs text-slate-400 mt-1 font-mono">
                    Report Filed: <span class="text-slate-300">{{ $incident->created_at->format('M d, Y - h:i A') }}</span>
                    <span class="text-slate-600 mx-1">&bull;</span>
                    <span class="text-cyan-400">{{ $incident->created_at->diffForHumans() }}</span>
                </p>
            </div>
            <div class="flex items-center space-x-2 shrink-0">
                <div class="px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-right">
                    <div class="text-[10px] uppercase font-mono text-slate-500">Classification</div>
                    <div class="text-xs font-bold text-cyan-300 font-mono">{{ $incident->incidentType->name ?? 'Unknown' }}</div>
                </div>
            </div>
        </div>

        {{-- 4-Stage Response Pipeline --}}
        <div id="citizen-pipeline-block">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs uppercase font-mono tracking-wider text-slate-400 font-semibold">Emergency Response Pipeline</span>
                <span class="text-[11px] font-mono text-cyan-400">Current Status: {{ $incident->status }}</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <!-- Step 1: Logged -->
                <div class="p-3 rounded-xl border bg-emerald-500/10 border-emerald-500/40 text-emerald-300">
                    <div class="w-6 h-6 rounded-full bg-emerald-500 text-slate-950 font-bold text-xs flex items-center justify-center mx-auto mb-1">&check;</div>
                    <div class="text-xs font-bold">1. Logged</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Received at station</div>
                </div>

                <!-- Step 2: Dispatched -->
                <div class="p-3 rounded-xl border
                    @if(in_array($incident->status, ['Dispatched', 'Responding', 'Resolved'])) bg-emerald-500/10 border-emerald-500/40 text-emerald-300
                    @else bg-slate-950 border-slate-800 text-slate-500 @endif">
                    <div class="w-6 h-6 rounded-full font-bold text-xs flex items-center justify-center mx-auto mb-1
                        @if(in_array($incident->status, ['Dispatched', 'Responding', 'Resolved'])) bg-emerald-500 text-slate-950
                        @else bg-slate-800 text-slate-400 @endif">
                        2
                    </div>
                    <div class="text-xs font-bold">2. Dispatched</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Specialist assigned</div>
                </div>

                <!-- Step 3: Responding -->
                <div class="p-3 rounded-xl border
                    @if(in_array($incident->status, ['Responding', 'Resolved'])) bg-emerald-500/10 border-emerald-500/40 text-emerald-300
                    @elseif($incident->status === 'Dispatched') bg-cyan-500/10 border-cyan-500/40 text-cyan-300 animate-pulse
                    @else bg-slate-950 border-slate-800 text-slate-500 @endif">
                    <div class="w-6 h-6 rounded-full font-bold text-xs flex items-center justify-center mx-auto mb-1
                        @if(in_array($incident->status, ['Responding', 'Resolved'])) bg-emerald-500 text-slate-950
                        @elseif($incident->status === 'Dispatched') bg-cyan-500 text-slate-950
                        @else bg-slate-800 text-slate-400 @endif">
                        3
                    </div>
                    <div class="text-xs font-bold">3. Responding</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">En route to location</div>
                </div>

                <!-- Step 4: Resolved -->
                <div class="p-3 rounded-xl border
                    @if($incident->status === 'Resolved') bg-emerald-500/10 border-emerald-500/40 text-emerald-300
                    @else bg-slate-950 border-slate-800 text-slate-500 @endif">
                    <div class="w-6 h-6 rounded-full font-bold text-xs flex items-center justify-center mx-auto mb-1
                        @if($incident->status === 'Resolved') bg-emerald-500 text-slate-950
                        @else bg-slate-800 text-slate-400 @endif">
                        4
                    </div>
                    <div class="text-xs font-bold">4. Resolved</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Entity neutralized</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. 2-COLUMN TACTICAL DESKTOP GRID (LEFT: DOSSIER & MAP | RIGHT: UPLOADED IMAGES & SPECIALIST) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT COLUMN: Details, Map, Witness Statement, Containment Dossier (Span 7) --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- Tactical Ground Zero Location & Interactive Map Card --}}
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-3 h-3 rounded-full bg-cyan-400"></div>
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider font-mono">Occurrence Location &amp; Ground Zero</h2>
                    </div>
                    @if($incident->hasCoordinates())
                        <div class="flex items-center space-x-2 text-[11px] font-mono">
                            <a href="https://www.openstreetmap.org/?mlat={{ $incident->latitude }}&mlon={{ $incident->longitude }}#map=16/{{ $incident->latitude }}/{{ $incident->longitude }}" target="_blank"
                               class="text-cyan-400 hover:text-cyan-300 transition-colors">
                                OpenStreetMap &rarr;
                            </a>
                            <span class="text-slate-600">&bull;</span>
                            <a href="https://www.google.com/maps?q={{ $incident->latitude }},{{ $incident->longitude }}" target="_blank"
                               class="text-slate-400 hover:text-slate-300 transition-colors">
                                Directions &rarr;
                            </a>
                        </div>
                    @endif
                </div>

                <div class="space-y-1">
                    <div class="text-base font-bold text-white flex items-center space-x-2">
                        <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $incident->location_address }}</span>
                    </div>
                    <div class="text-xs font-mono text-slate-400 pl-6">{{ $incident->location_city }}</div>
                </div>

                @if($incident->hasCoordinates())
                    {{-- Interactive Leaflet Map --}}
                    <div class="rounded-xl overflow-hidden border border-slate-700 relative">
                        <div id="incident-display-map" class="h-64 sm:h-80 w-full relative z-0"></div>
                        <div class="bg-slate-950 px-3.5 py-2 flex items-center justify-between text-[11px] font-mono text-slate-400 border-t border-slate-800">
                            <span class="flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span class="text-slate-300">Lat: {{ number_format($incident->latitude, 5) }}, Lng: {{ number_format($incident->longitude, 5) }}</span>
                            </span>
                            <span class="text-emerald-400 font-bold">&bull; Ground Zero Locked</span>
                        </div>
                    </div>
                @else
                    {{-- Alert when GPS pin was omitted --}}
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 space-y-2.5">
                        <div class="flex items-start space-x-2.5">
                            <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <p class="font-bold text-amber-300 text-xs">No Tactical Map Pin Dropped</p>
                                <p class="text-[11px] text-slate-300 mt-0.5 leading-relaxed">
                                    You did not drop a GPS pinpoint when submitting this emergency report. Dispatchers use exact map coordinates for precise team deployment.
                                </p>
                            </div>
                        </div>
                        @if($incident->status === 'Pending')
                            <div class="pt-1">
                                <a href="{{ route('citizen.incidents.edit', $incident->id) }}"
                                    class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-mono font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>Click Here to Drop Ground Zero Pin &rarr;</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Phenomenon Description Card --}}
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-xl space-y-3">
                <div class="flex items-center space-x-2.5 pb-2 border-b border-slate-800">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider font-mono">Phenomenon Description &amp; Witness Statement</h2>
                </div>
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-sm text-slate-200 leading-relaxed whitespace-pre-line font-sans">
                    {{ $incident->description }}
                </div>
            </div>

            {{-- Response / Containment Dossier (If Resolved) --}}
            @if($incident->responseReport)
                @php $rep = $incident->responseReport; @endphp
                <div class="rounded-2xl bg-emerald-950/20 border border-emerald-500/30 p-6 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-emerald-500/20 pb-3">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                            <h3 class="text-base font-bold text-emerald-300">Official Containment &amp; Resolution Dossier</h3>
                        </div>
                        <span class="text-xs font-mono px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-bold">Neutralized</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-emerald-500/20">
                            <span class="text-slate-400 font-mono uppercase block text-[10px]">Containment Protocol</span>
                            <span class="font-bold text-white text-sm mt-0.5 block">{{ $rep->containment_method }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-emerald-500/20">
                            <span class="text-slate-400 font-mono uppercase block text-[10px]">Neutralization Timestamp</span>
                            <span class="font-semibold text-slate-200 text-sm mt-0.5 block">{{ $rep->submitted_at ? $rep->submitted_at->format('M d, Y - h:i A') : $rep->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 font-mono uppercase block text-[10px] mb-1">Field Findings &amp; Entity Analysis</span>
                        <p class="text-xs text-slate-200 bg-slate-950/80 p-3.5 rounded-xl border border-emerald-500/20 leading-relaxed whitespace-pre-line">
                            {{ $rep->findings_summary }}
                        </p>
                    </div>

                    @if($rep->recommendations)
                        <div>
                            <span class="text-slate-400 font-mono uppercase block text-[10px] mb-1">Safety Recommendations for Occupants</span>
                            <p class="text-xs text-slate-300 italic bg-slate-950/40 p-3 rounded-xl border border-emerald-500/20">
                                {{ $rep->recommendations }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

        </div>

        {{-- RIGHT COLUMN: UPLOADED IMAGES ON RIGHT SIDE + FIELD SPECIALIST (Span 5) --}}
        <div class="lg:col-span-5 space-y-6">

            {{-- 1. UPLOADED EVIDENCE IMAGES (LOCATED ON THE RIGHT SIDE) --}}
            <div class="rounded-2xl bg-slate-900 border border-cyan-500/30 p-6 shadow-xl shadow-cyan-950/20 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-3 h-3 rounded-full bg-cyan-400"></div>
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider font-mono">Uploaded Evidence / Photos</h2>
                    </div>
                    <span class="text-xs font-mono px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                        {{ $incident->evidence->count() }} Attached
                    </span>
                </div>

                @if($incident->evidence->count() > 0)
                    {{-- Gallery of Uploaded Evidence Images --}}
                    <div class="space-y-4">
                        @foreach($incident->evidence as $index => $item)
                            <div class="rounded-xl overflow-hidden bg-slate-950 border border-slate-800 shadow-lg group hover:border-cyan-500/50 transition-all">
                                @if(in_array(strtolower(pathinfo($item->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']))
                                    {{-- Photo Preview with Zoom link --}}
                                    <div class="relative overflow-hidden bg-slate-900">
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank" title="Click to view full image in new tab" class="block cursor-zoom-in">
                                            <img src="{{ asset('storage/' . $item->file_path) }}"
                                                 alt="{{ $item->caption ?? 'Evidence image' }}"
                                                 class="w-full max-h-72 object-contain sm:object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                                <span class="text-xs font-mono text-cyan-300 flex items-center space-x-1.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"/>
                                                    </svg>
                                                    <span>Open full resolution image &rarr;</span>
                                                </span>
                                            </div>
                                        </a>
                                    </div>
                                @else
                                    {{-- Document or non-image artifact --}}
                                    <div class="p-6 flex flex-col items-center justify-center text-center space-y-2 bg-slate-900/60">
                                        <svg class="w-10 h-10 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="text-xs font-mono text-slate-300 font-bold uppercase">{{ pathinfo($item->file_path, PATHINFO_EXTENSION) }} Document File</span>
                                        <a href="{{ asset('storage/' . $item->file_path) }}" target="_blank"
                                           class="px-3 py-1.5 rounded-lg bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/40 text-xs font-mono transition-colors">
                                            Download / View Document &rarr;
                                        </a>
                                    </div>
                                @endif

                                {{-- Image Caption & Info Bar --}}
                                <div class="p-3 bg-slate-950 border-t border-slate-800/80 space-y-1">
                                    <div class="flex items-center justify-between text-[11px] font-mono">
                                        <span class="text-cyan-400 font-bold uppercase">Evidence Item #{{ $index + 1 }}</span>
                                        <span class="text-slate-500">{{ $item->created_at ? $item->created_at->format('M d, Y') : '' }}</span>
                                    </div>
                                    <p class="text-xs text-white font-medium">
                                        {{ $item->caption ?: ($item->file_name ?? 'Photographic anomaly captured on-site') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Empty state when no images were uploaded --}}
                    <div class="p-8 rounded-xl bg-slate-950/60 border border-dashed border-slate-800 text-center space-y-3">
                        <div class="w-12 h-12 rounded-xl bg-slate-800/80 text-slate-500 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-300">No Photographic Evidence Attached</p>
                            <p class="text-[11px] text-slate-500 mt-0.5 leading-relaxed">
                                No anomaly photos or documents were provided during report intake.
                            </p>
                        </div>
                        @if($incident->status === 'Pending')
                            <div class="pt-1">
                                <a href="{{ route('citizen.incidents.edit', $incident->id) }}"
                                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-mono font-bold bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/40 transition-colors">
                                    <span>+ Add Photo Evidence &rarr;</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- 2. ASSIGNED SPECIALIST CARD --}}
            <div id="citizen-specialist-block" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-3 h-3 rounded-full bg-indigo-400"></div>
                        <h2 class="text-sm font-bold text-white uppercase tracking-wider font-mono">Assigned Field Specialist</h2>
                    </div>
                    <span class="text-xs font-mono text-slate-400">Unit Status</span>
                </div>

                @if($incident->latestAssignment && $incident->latestAssignment->responder)
                    @php $resp = $incident->latestAssignment->responder; @endphp
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-white text-base leading-tight">{{ $resp->user->name }}</h3>
                                <div class="text-xs text-cyan-400 font-medium mt-0.5">{{ $resp->specialization }}</div>
                            </div>
                            <span class="text-xs font-mono px-2.5 py-1 rounded-lg bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 font-bold shrink-0">
                                {{ $resp->badge_number }}
                            </span>
                        </div>

                        <div class="pt-2 border-t border-slate-800/80 space-y-1.5 text-xs">
                            <div class="flex items-center space-x-2 text-slate-300">
                                <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>Emergency Contact: <span class="font-mono text-white font-semibold">{{ $resp->user->phone ?? 'HQ Radio Channel' }}</span></span>
                            </div>
                        </div>

                        @if($incident->latestAssignment->dispatcher_notes)
                            <div class="p-3 rounded-lg bg-slate-900 border border-amber-500/20 text-xs space-y-1">
                                <span class="text-amber-400 font-mono uppercase text-[10px] font-bold block">Dispatcher Directives:</span>
                                <p class="text-slate-300 leading-relaxed">{{ $incident->latestAssignment->dispatcher_notes }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-6 rounded-xl bg-slate-950/40 border border-dashed border-slate-800 text-center space-y-2">
                        <span class="w-3 h-3 rounded-full bg-amber-400 inline-block animate-ping"></span>
                        <div class="text-xs font-semibold text-amber-300">Pending Specialist Assignment</div>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            A dispatcher is reviewing this emergency and assigning the appropriate specialist for on-site containment.
                        </p>
                    </div>
                @endif
            </div>

            {{-- 3. CASE SUMMARY METADATA CARD --}}
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 shadow-xl space-y-3 text-xs font-mono">
                <span class="text-slate-500 uppercase text-[10px] tracking-wider block font-bold">Case Summary</span>
                <div class="divide-y divide-slate-800/80">
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Tracking Code</span>
                        <span class="text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Reporting Citizen</span>
                        <span class="text-slate-200">{{ $incident->citizen->name ?? 'Citizen' }}</span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">GPS Coordinates</span>
                        <span class="{{ $incident->hasCoordinates() ? 'text-emerald-400' : 'text-amber-400' }}">
                            {{ $incident->hasCoordinates() ? 'Locked (2D Pin)' : 'Omitted' }}
                        </span>
                    </div>
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-400">Last System Update</span>
                        <span class="text-slate-400">{{ $incident->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@if($incident->hasCoordinates())
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #incident-display-map .leaflet-tile-pane {
        filter: invert(1) hue-rotate(180deg) brightness(0.85) saturate(0.9);
    }
    #incident-display-map .leaflet-marker-pane,
    #incident-display-map .leaflet-popup-pane {
        filter: invert(1) hue-rotate(180deg);
    }
    .leaflet-popup-content-wrapper {
        background: #0f172a !important;
        color: #f1f5f9 !important;
        border: 1px solid #334155 !important;
        border-radius: 0.75rem !important;
        font-family: 'Inter', sans-serif !important;
    }
    .leaflet-popup-tip { background: #0f172a !important; }
</style>
@endpush
@endif

@push('scripts')
@if($incident->hasCoordinates())
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $incident->latitude }};
        const lng = {{ $incident->longitude }};

        const map = L.map('incident-display-map', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([lat, lng], 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup(`
            <div class="text-xs font-mono font-bold text-cyan-400 mb-0.5">{{ $incident->tracking_code }}</div>
            <div class="text-xs font-bold text-white">{{ $incident->title }}</div>
            <div class="text-[10px] text-slate-400 mt-1">{{ $incident->location_address }}</div>
        `).openPopup();
    });
</script>
@endif

<script>
    // Real-time Incident Status Updates via Laravel Echo / WebSockets (Zero Tab Reload)
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.channel('incident.{{ $incident->id }}')
                .listen('.IncidentStatusUpdated', function(event) {
                    handleLiveStatusUpdate(event.incident || event);
                })
                .listen('IncidentStatusUpdated', function(event) {
                    handleLiveStatusUpdate(event.incident || event);
                });
        }

        // Silent background refresh: Updates DOM without reloading the tab
        async function refreshCitizenIncidentSilently() {
            try {
                const response = await fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) return;
                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');

                const ids = ['citizen-header-actions', 'citizen-pipeline-block', 'citizen-specialist-block'];
                ids.forEach(id => {
                    const newEl = doc.getElementById(id);
                    const curEl = document.getElementById(id);
                    if (newEl && curEl) curEl.innerHTML = newEl.innerHTML;
                });
            } catch (e) {
                console.warn('Citizen status sync notice:', e);
            }
        }

        function handleLiveStatusUpdate(inc) {
            if (typeof showLiveToast === 'function') {
                showLiveToast(
                    '⚡ EMERGENCY STATUS UPDATED',
                    'Tracking ' + (inc.tracking_code || '{{ $incident->tracking_code }}') + ' is now ' + (inc.status || 'Updated') + '. Response pipeline synced.'
                );
            }
            // Silent DOM update without reloading the tab!
            refreshCitizenIncidentSilently();
        }

        // Safety fallback: periodic silent check every 25s without reloading the tab
        setInterval(function() {
            if (!document.hidden) {
                refreshCitizenIncidentSilently();
            }
        }, 25000);
    });
</script>
@endpush
