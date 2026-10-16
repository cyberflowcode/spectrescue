@extends('layouts.app')

@section('title', 'Triage & Dispatch: ' . $incident->tracking_code)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.incidents.index') : route('dispatcher.dashboard') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1 transition-colors">
            <span>&larr; Back to {{ auth()->user()->isAdmin() ? 'Manage Incidents' : 'Dispatch Console' }}</span>
        </a>
        <div class="flex items-center space-x-2">
            <span class="text-xs px-2.5 py-0.5 rounded-full font-mono font-semibold {{ $incident->threatBadgeClass() }}">
                Threat: {{ $incident->threat_level }}
            </span>
            <span class="text-xs px-2.5 py-0.5 rounded-full font-mono font-semibold {{ $incident->statusBadgeClass() }}">
                Status: {{ $incident->status }}
            </span>
        </div>
    </div>

    <!-- Main Dispatch Console Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Incident Dossier & Evidence -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Incident Overview Card -->
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
                <div class="border-b border-slate-800 pb-4">
                    <span class="text-xs font-mono text-amber-400 font-semibold tracking-wider uppercase">{{ $incident->tracking_code }}</span>
                    <h1 class="text-2xl font-bold text-white mt-1">{{ $incident->title }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400 mt-2 font-mono">
                        <span>Classification: <strong class="text-slate-200">{{ $incident->incidentType->name ?? 'Unclassified' }}</strong></span>
                        <span>&bull;</span>
                        <span>Reported: {{ $incident->created_at->format('M d, Y h:i A') }} ({{ $incident->created_at->diffForHumans() }})</span>
                    </div>
                </div>

                <!-- Caller / Location Info -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                        <span class="font-mono uppercase text-slate-500 text-[10px] block">Reporting Caller (Citizen)</span>
                        <div class="text-sm font-bold text-white">{{ $incident->citizen->name ?? 'Unknown Citizen' }}</div>
                        <div class="text-cyan-400 font-mono">{{ $incident->citizen->phone ?? 'No phone logged' }}</div>
                        <div class="text-slate-400 truncate">{{ $incident->citizen->email ?? '' }}</div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-mono uppercase text-slate-500 text-[10px] block">Location Coordinates</span>
                            @if($incident->hasCoordinates())
                                <a href="https://www.openstreetmap.org/?mlat={{ $incident->latitude }}&mlon={{ $incident->longitude }}#map=16/{{ $incident->latitude }}/{{ $incident->longitude }}" target="_blank"
                                   class="text-[10px] font-mono text-cyan-400 hover:text-cyan-300">
                                    OpenStreetMap &rarr;
                                </a>
                            @endif
                        </div>
                        <div class="text-sm font-bold text-white">{{ $incident->location_address }}</div>
                        <div class="text-slate-300">{{ $incident->location_city }}</div>
                        @if($incident->hasCoordinates())
                            <div class="text-[10px] text-cyan-400 font-mono">GPS: {{ number_format($incident->latitude, 4) }}, {{ number_format($incident->longitude, 4) }}</div>
                            <div id="dispatcher-incident-map" class="h-36 w-full rounded-lg overflow-hidden border border-slate-800 mt-2 relative z-0"></div>
                        @else
                            <div class="text-[10px] text-slate-500 font-mono">Sector: Metropolitan Zone</div>
                        @endif
                    </div>
                </div>

                <!-- Phenomenon Description -->
                <div class="space-y-2">
                    <span class="text-xs font-mono uppercase text-slate-400">Caller Incident Narrative</span>
                    <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 text-xs text-slate-200 leading-relaxed whitespace-pre-line">
                        {{ $incident->description }}
                    </div>
                </div>

                <!-- Evidence Artifacts -->
                @if($incident->evidence->count() > 0)
                    <div class="space-y-3 pt-2">
                        <span class="text-xs font-mono uppercase text-slate-400">Uploaded Field Evidence</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($incident->evidence as $evidence)
                                <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5">
                                    @if(in_array(pathinfo($evidence->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp']))
                                        <img src="{{ asset('storage/' . $evidence->file_path) }}" alt="Evidence" class="w-full h-44 object-cover rounded-lg border border-slate-800">
                                    @else
                                        <div class="h-32 flex items-center justify-center bg-slate-900 rounded-lg text-xs font-mono text-cyan-400 border border-slate-800">
                                            [Document Evidence]
                                        </div>
                                    @endif
                                    <div class="text-xs text-slate-300 truncate">{{ $evidence->caption ?? 'Evidence Artifact #' . $evidence->id }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- Assignment History Log -->
            @if($incident->assignments->count() > 0)
                <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
                    <h3 class="font-bold text-white text-base">Dispatch Assignment Log ({{ $incident->assignments->count() }})</h3>
                    <div class="space-y-3">
                        @foreach($incident->assignments as $assignment)
                            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="font-bold text-white text-sm">
                                        Assigned to: <span class="text-cyan-400">{{ $assignment->responder->user->name ?? 'Responder' }}</span>
                                        <span class="text-slate-400 font-mono text-xs">({{ $assignment->responder->specialization ?? '' }})</span>
                                    </div>
                                    <span class="font-mono text-xs px-2 py-0.5 rounded 
                                        @if($assignment->status === 'accepted') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                        @elseif($assignment->status === 'declined') bg-red-500/20 text-red-300 border border-red-500/30
                                        @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
                                        {{ ucfirst($assignment->status) }}
                                    </span>
                                </div>
                                <div class="text-slate-400 font-mono text-[11px]">
                                    Dispatched by {{ $assignment->dispatcher->name ?? 'Dispatcher' }} on {{ $assignment->created_at->format('M d, Y h:i A') }}
                                </div>
                                @if($assignment->dispatcher_notes)
                                    <div class="text-slate-300 bg-slate-900/60 p-2 rounded border border-slate-800">
                                        <strong>Dispatcher Directive:</strong> {{ $assignment->dispatcher_notes }}
                                    </div>
                                @endif
                                @if($assignment->responder_notes)
                                    <div class="text-slate-300 bg-slate-900/60 p-2 rounded border border-slate-800">
                                        <strong>Responder Feedback:</strong> {{ $assignment->responder_notes }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Col: Dispatch Actions & Threat Control -->
        <div class="space-y-6">

            @if($incident->status === 'Resolved')
                <!-- Incident Resolved Card -->
                <div class="rounded-2xl bg-slate-900 border border-emerald-500/40 p-6 space-y-4 shadow-xl shadow-emerald-950/20">
                    <div class="flex items-center space-x-2 text-emerald-400">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-base font-bold text-white">Emergency Mission Resolved</h2>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        This paranormal emergency has been contained and closed. The field specialist has submitted the final containment dossier.
                    </p>
                    @if($incident->responseReport)
                        <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 text-xs space-y-1 font-mono">
                            <div class="text-slate-400 text-[11px]">Specialist: <span class="text-white">{{ $incident->responseReport->responder->user->name ?? 'Field Unit' }}</span></div>
                            <div class="text-slate-400 text-[11px]">Method: <span class="text-emerald-400">{{ $incident->responseReport->containment_method }}</span></div>
                        </div>
                    @endif
                </div>

            @elseif($activeAssignment)
                <!-- Active Deployment Card (Specialist Already Assigned) -->
                <div class="rounded-2xl bg-slate-900 border border-cyan-500/40 p-6 space-y-5 shadow-xl shadow-cyan-950/20">
                    <div class="border-b border-slate-800 pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Active Mission</span>
                            <h2 class="text-lg font-bold text-white mt-1">Specialist Deployed</h2>
                        </div>
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-cyan-500"></span>
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-bold text-white">{{ $activeAssignment->responder->user->name }}</div>
                                <div class="text-cyan-400 font-mono text-[11px]">{{ $activeAssignment->responder->badge_number }} &bull; {{ $activeAssignment->responder->specialization }}</div>
                            </div>
                            <span class="font-mono text-xs px-2.5 py-1 rounded 
                                @if($activeAssignment->status === 'accepted') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif">
                                {{ $activeAssignment->status === 'accepted' ? 'En Route / Responding' : 'Awaiting Response' }}
                            </span>
                        </div>

                        <div class="text-slate-400 font-mono text-[11px] pt-1 border-t border-slate-900">
                            Dispatched on: {{ $activeAssignment->assigned_at ? $activeAssignment->assigned_at->format('M d, Y h:i A') : $activeAssignment->created_at->format('M d, Y h:i A') }}
                        </div>

                        @if($activeAssignment->dispatcher_notes)
                            <div class="p-2.5 rounded bg-slate-900 border border-slate-800 text-slate-300 text-[11px]">
                                <strong class="text-amber-400">Directive:</strong> {{ $activeAssignment->dispatcher_notes }}
                            </div>
                        @endif
                    </div>

                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-400 space-y-1">
                        <div class="text-cyan-400 font-semibold font-mono text-[11px]">MISSION DEPLOYMENT LOCK</div>
                        <p class="text-[11px] leading-relaxed">
                            This specialist is currently assigned to this emergency. A responder can only take one active deployment at a time.
                        </p>
                    </div>

                    <!-- Recall / Reassign Button -->
                    <form method="POST" action="{{ route('dispatcher.incidents.assignments.cancel', [$incident->id, $activeAssignment->id]) }}" onsubmit="return confirm('Recall this deployment? The responder will become available again and the incident returned to Pending.');">
                        @csrf
                        <button type="submit" class="w-full py-2.5 px-3 rounded-xl text-xs font-semibold bg-slate-950 hover:bg-red-950/40 text-red-400 hover:text-red-300 border border-red-500/30 transition-all flex items-center justify-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Recall Deployment &amp; Free Specialist</span>
                        </button>
                    </form>
                </div>

            @else
                <!-- Dispatch Specialist Form (Available when Incident is Pending) -->
                <div class="rounded-2xl bg-slate-900 border border-amber-500/40 p-6 space-y-5 shadow-xl shadow-amber-950/20">
                    <div class="border-b border-slate-800 pb-3">
                        <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">Action Console</span>
                        <h2 class="text-lg font-bold text-white mt-1">Dispatch Response Unit</h2>
                        <p class="text-xs text-slate-400">Select an accredited field specialist on standby to deploy to this emergency.</p>
                    </div>

                    @if($availableResponders->isEmpty())
                        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs space-y-1">
                            <div class="font-bold flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>No Specialists Currently Available</span>
                            </div>
                            <p class="text-[11px] text-slate-300 leading-relaxed">
                                All approved field specialists are currently deployed on active missions. Once a mission is resolved, the responder will be free for new deployments.
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('dispatcher.incidents.assign', $incident->id) }}" class="space-y-4">
                        @csrf

                        <!-- Select Responder -->
                        <div class="space-y-1">
                            <label for="responder_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                                Available Field Specialist <span class="text-red-400">*</span>
                            </label>
                            <select id="responder_id" name="responder_id" required {{ $availableResponders->isEmpty() ? 'disabled' : '' }}
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                                @if($availableResponders->isEmpty())
                                    <option value="" disabled selected>No specialists on standby (all deployed)</option>
                                @else
                                    <option value="">Select Verified Specialist...</option>
                                    @foreach($availableResponders as $resp)
                                        <option value="{{ $resp->id }}">
                                            {{ $resp->user->name }} &bull; {{ $resp->specialization }} ({{ $resp->badge_number }}) {{ $resp->user->isOnline() ? '● [ONLINE]' : '○ [OFFLINE]' }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <!-- Threat Reclassification -->
                        <div class="space-y-1">
                            <label for="threat_level" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                                Confirm / Upgrade Threat Level
                            </label>
                            <select id="threat_level" name="threat_level"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-xs font-mono">
                                <option value="Low" {{ $incident->threat_level == 'Low' ? 'selected' : '' }}>Low Threat</option>
                                <option value="Medium" {{ $incident->threat_level == 'Medium' ? 'selected' : '' }}>Medium Threat</option>
                                <option value="High" {{ $incident->threat_level == 'High' ? 'selected' : '' }}>High Threat</option>
                                <option value="Critical" {{ $incident->threat_level == 'Critical' ? 'selected' : '' }}>Critical Threat (Immediate Hazard)</option>
                            </select>
                        </div>

                        <!-- Dispatcher Directive / Notes -->
                        <div class="space-y-1">
                            <label for="dispatcher_notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                                Dispatcher Directive / Mission Notes
                            </label>
                            <textarea id="dispatcher_notes" name="dispatcher_notes" rows="3" placeholder="Provide tactical notes, equipment directives, or special precautions for the responder..."
                                class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-xs">{{ old('dispatcher_notes') }}</textarea>
                        </div>

                        <!-- Dispatch Submit Button -->
                        @if($availableResponders->isNotEmpty())
                            <button type="submit" class="w-full py-3 rounded-xl font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-lg shadow-amber-500/25 transition-all text-sm flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>Deploy Specialist Now &rarr;</span>
                            </button>
                        @else
                            <button type="button" disabled class="w-full py-3 rounded-xl font-bold bg-slate-800 text-slate-500 text-sm cursor-not-allowed">
                                Standby: All Specialists Deployed
                            </button>
                        @endif
                    </form>
                </div>
            @endif

            <!-- Threat Assessment Card -->
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 space-y-3">
                <span class="text-xs font-mono uppercase text-slate-400">Emergency Protocol Guidance</span>
                <ul class="text-xs space-y-2 text-slate-300 list-disc list-inside">
                    <li><strong>Critical:</strong> Active demonic possession or physical injury. Dispatch Exorcist immediately.</li>
                    <li><strong>High:</strong> Flying objects, rapid temperature collapse. Dispatch Ghost Hunter with containment traps.</li>
                    <li><strong>Medium:</strong> Unsettling voices, relic anomalous pulses. Deploy medium or relic specialist.</li>
                    <li><strong>Low:</strong> Peripheral shadow apparitions or battery drainage. Single researcher response.</li>
                </ul>
            </div>

        </div>

    </div>

</div>
@endsection

@if($incident->hasCoordinates())
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
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $incident->latitude }};
        const lng = {{ $incident->longitude }};

        const map = L.map('dispatcher-incident-map', {
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
            <div class="text-xs font-mono font-bold text-amber-400">{{ $incident->tracking_code }}</div>
            <div class="text-xs text-white">{{ $incident->title }}</div>
        `).openPopup();
    });
</script>
@endpush
@endif
