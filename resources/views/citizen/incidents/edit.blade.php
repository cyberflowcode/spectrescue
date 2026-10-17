@extends('layouts.app')

@section('title', 'Edit Report: ' . $incident->tracking_code)

@section('content')
<div class="w-full space-y-6">

    {{-- Top Action Bar / Breadcrumb --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('citizen.incidents.show', $incident->id) }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
                <span>&larr; Back to Incident Details</span>
            </a>
            <span class="text-slate-600">/</span>
            <span class="text-xs font-mono text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-xs px-2.5 py-1 rounded-full font-mono font-semibold {{ $incident->threatBadgeClass() }}">
                Threat: {{ $incident->threat_level }}
            </span>
            <span class="text-xs px-2.5 py-1 rounded-full font-mono font-semibold {{ $incident->statusBadgeClass() }}">
                {{ $incident->status }}
            </span>
        </div>
    </div>

    {{-- Header Banner --}}
    <div class="border-b border-slate-800 pb-4">
        <div class="flex items-center space-x-2 mb-1">
            <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-pulse"></span>
            <span class="text-xs font-mono uppercase tracking-widest text-cyan-400">Update Report &amp; Tactical Pin</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Edit Incident: {{ $incident->title }}</h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
            Update your incident description, address, or drop/drag the map pin to lock in ground zero coordinates.
        </p>
    </div>

    {{-- Edit Form --}}
    <form method="POST" action="{{ route('citizen.incidents.update', $incident->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- 2 Column Grid for Full Desktop Web View --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            {{-- LEFT COLUMN: Incident Form Fields --}}
            <div class="lg:col-span-6 space-y-6 bg-slate-900 border border-slate-800 rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl shadow-slate-950/50">

                <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-800">
                    <div class="w-3 h-3 rounded-full bg-cyan-400"></div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider font-mono">1. Emergency Report Details</h2>
                </div>

                <!-- Incident Type -->
                <div class="space-y-1.5">
                    <label for="incident_type_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Phenomenon Classification <span class="text-red-400">*</span>
                    </label>
                    <select id="incident_type_id" name="incident_type_id" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                        @foreach($incidentTypes as $type)
                            <option value="{{ $type->id }}" data-threat="{{ $type->default_threat_level }}" {{ old('incident_type_id', $incident->incident_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} (Default Threat: {{ $type->default_threat_level }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Title -->
                <div class="space-y-1.5">
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Emergency Headline / Title <span class="text-red-400">*</span>
                    </label>
                    <input id="title" type="text" name="title" value="{{ old('title', $incident->title) }}" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                </div>

                <!-- Threat Level -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Perceived Threat Level <span class="text-red-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-emerald-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                            <input type="radio" name="threat_level" value="Low" {{ old('threat_level', $incident->threat_level) == 'Low' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500">
                            <span class="text-xs font-medium text-emerald-400">Low</span>
                        </label>
                        <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-yellow-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                            <input type="radio" name="threat_level" value="Medium" {{ old('threat_level', $incident->threat_level) == 'Medium' ? 'checked' : '' }} class="text-yellow-500 focus:ring-yellow-500">
                            <span class="text-xs font-medium text-yellow-400">Medium</span>
                        </label>
                        <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-orange-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                            <input type="radio" name="threat_level" value="High" {{ old('threat_level', $incident->threat_level) == 'High' ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500">
                            <span class="text-xs font-medium text-orange-400">High</span>
                        </label>
                        <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-red-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                            <input type="radio" name="threat_level" value="Critical" {{ old('threat_level', $incident->threat_level) == 'Critical' ? 'checked' : '' }} class="text-red-500 focus:ring-red-500">
                            <span class="text-xs font-medium text-red-400">Critical</span>
                        </label>
                    </div>
                </div>

                <!-- Location Details (Street + City) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="location_address" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Physical Address / Street <span class="text-red-400">*</span>
                        </label>
                        <input id="location_address" type="text" name="location_address" value="{{ old('location_address', $incident->location_address) }}" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                    </div>

                    <div class="space-y-1.5">
                        <label for="location_city" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            City / District <span class="text-red-400">*</span>
                        </label>
                        <input id="location_city" type="text" name="location_city" value="{{ old('location_city', $incident->location_city) }}" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                    </div>
                </div>

                <!-- Detailed Description -->
                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Detailed Phenomenon Description <span class="text-red-400">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">{{ old('description', $incident->description) }}</textarea>
                </div>

                <!-- Existing & Additional Evidence -->
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                    @if($incident->evidence->count() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                                    Current Attached Evidence ({{ $incident->evidence->count() }})
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">Click ✕ to remove</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                @foreach($incident->evidence as $ev)
                                    <div class="relative p-2.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between gap-2 hover:border-slate-700 transition-colors">
                                        <div class="flex items-center space-x-2.5 overflow-hidden">
                                            @if(in_array(pathinfo($ev->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'webp']))
                                                <img src="{{ asset('storage/' . $ev->file_path) }}" class="w-12 h-12 object-cover rounded-lg border border-slate-800 shrink-0" alt="Thumbnail">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] font-mono text-cyan-400 shrink-0 border border-slate-700">DOC</div>
                                            @endif
                                            <div class="truncate text-xs">
                                                <p class="font-medium text-slate-200 truncate" title="{{ $ev->file_name }}">{{ $ev->file_name }}</p>
                                                <p class="text-[10px] text-slate-400 truncate">{{ $ev->caption ?? 'Evidence image' }}</p>
                                            </div>
                                        </div>

                                        {{-- Delete Button with X --}}
                                        <button type="submit"
                                            form="delete-evidence-{{ $ev->id }}"
                                            title="Remove this uploaded image"
                                            class="w-7 h-7 rounded-lg bg-red-500/15 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 flex items-center justify-center text-sm font-bold transition-all shrink-0 cursor-pointer shadow-sm">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="space-y-1.5 pt-2 {{ $incident->evidence->count() > 0 ? 'border-t border-slate-800' : '' }}">
                        <label for="evidence_file" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            {{ $incident->evidence->count() > 0 ? 'Attach Additional Evidence Photo (Optional)' : 'Upload Evidence / Anomaly Photo (Optional)' }}
                        </label>
                        <input id="evidence_file" type="file" name="evidence_file" accept="image/*,.pdf"
                            class="block w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 cursor-pointer">
                        <span class="block text-[11px] text-slate-500">Supports JPG, PNG, WEBP, or PDF up to 10MB.</span>

                        {{-- Preview of newly selected file with X to cancel/remove before saving --}}
                        <div id="new-file-preview-container" class="hidden p-2.5 rounded-xl bg-slate-900 border border-cyan-500/30 flex items-center justify-between mt-2">
                            <div class="flex items-center space-x-2.5 overflow-hidden">
                                <img id="new-file-preview-img" src="" class="w-10 h-10 object-cover rounded-lg border border-slate-700 hidden" alt="Preview">
                                <div id="new-file-preview-doc" class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] font-mono text-cyan-400 border border-slate-700 hidden">DOC</div>
                                <div class="truncate text-xs">
                                    <span class="text-[10px] uppercase font-mono text-cyan-400 block font-bold">Selected File</span>
                                    <span id="new-file-name" class="font-medium text-white truncate block"></span>
                                </div>
                            </div>
                            <button type="button" id="btn-clear-new-file" title="Clear selected file" class="px-2.5 py-1 rounded-lg bg-red-500/15 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 text-xs font-mono font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                <span>&times;</span>
                                <span>Remove</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="evidence_caption" class="block text-xs font-medium text-slate-400">Photo Caption / Note</label>
                        <input id="evidence_caption" type="text" name="evidence_caption" value="{{ old('evidence_caption') }}" placeholder="e.g. Additional photo of door scratches"
                            class="w-full px-3.5 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs">
                    </div>
                </div>

                <!-- Submit / Action Buttons (Desktop Only; on mobile it appears below the tactical map pin) -->
                <div class="hidden lg:flex items-center space-x-3 pt-2">
                    <button type="submit" class="flex-1 py-3.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/25 transition-all flex items-center justify-center space-x-2 text-sm cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Save &amp; Update Incident Report</span>
                    </button>
                    <a href="{{ route('citizen.incidents.show', $incident->id) }}"
                        class="px-5 py-3.5 rounded-xl font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-sm transition-colors">
                        Cancel
                    </a>
                </div>

            </div>

            {{-- RIGHT COLUMN: Tactical Map Card (Sticky on Desktop) --}}
            <div class="lg:col-span-6 lg:sticky lg:top-20 space-y-4">

                <div class="rounded-2xl bg-slate-900 border border-cyan-500/30 p-4 sm:p-5 shadow-xl shadow-cyan-950/20 space-y-4">

                    {{-- Map Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3.5">
                        <div class="flex items-center space-x-3">
                            <div class="relative shrink-0">
                                <span class="w-3 h-3 rounded-full bg-cyan-400 block"></span>
                                <span class="w-3 h-3 rounded-full bg-cyan-400 animate-ping absolute inset-0"></span>
                            </div>
                            <div>
                                <h2 class="font-bold text-white text-sm uppercase tracking-wider font-mono">2. Tactical Ground Zero Map Pin</h2>
                                <p class="text-[11px] text-slate-400">Click map or drag the pin to set the exact coordinates</p>
                            </div>
                        </div>

                        <button type="button" id="btn-detect-gps"
                            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/40 text-xs font-mono transition-colors shrink-0">
                            <svg class="w-3.5 h-3.5 text-cyan-400 animate-spin hidden" id="gps-spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <svg class="w-3.5 h-3.5 text-cyan-400" id="gps-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Use My Current GPS</span>
                        </button>
                    </div>

                    {{-- Notice banner if coordinates were previously missing --}}
                    @if(!$incident->hasCoordinates())
                        <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs flex items-center space-x-2.5">
                            <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>No pin was previously saved! Drag the marker or click the map below to drop your pin.</span>
                        </div>
                    @endif

                    {{-- Leaflet Map Canvas --}}
                    <div id="incident-map" class="h-80 sm:h-96 lg:h-[480px] w-full rounded-xl overflow-hidden border border-slate-700 relative z-0"></div>

                    {{-- Coordinates readout & status bar --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] font-mono px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl gap-2">
                        <span id="map-status" class="text-slate-400 truncate">
                            {{ $incident->hasCoordinates() ? 'Pin locked at current coordinates' : 'Pin placed &bull; Drag to set location' }}
                        </span>
                        <div class="flex items-center space-x-3 text-cyan-400 shrink-0">
                            <span>Lat: <span id="display-lat" class="font-bold">{{ $incident->hasCoordinates() ? number_format($incident->latitude, 4) : '14.6091' }}</span></span>
                            <span class="text-slate-600">&bull;</span>
                            <span>Lng: <span id="display-lng" class="font-bold">{{ $incident->hasCoordinates() ? number_format($incident->longitude, 4) : '121.0223' }}</span></span>
                        </div>
                    </div>

                    {{-- Hidden form inputs for coordinates --}}
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $incident->latitude ?? '14.6091000') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $incident->longitude ?? '121.0223000') }}">

                    {{-- Mobile Save Action Buttons --}}
                    <div class="lg:hidden flex items-center space-x-3 pt-2">
                        <button type="submit" class="flex-1 py-3.5 rounded-xl font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-500/25 transition-all flex items-center justify-center space-x-2 text-sm cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Save &amp; Update Incident</span>
                        </button>
                        <a href="{{ route('citizen.incidents.show', $incident->id) }}"
                            class="px-4 py-3.5 rounded-xl font-medium bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs transition-colors">
                            Cancel
                        </a>
                    </div>
                </div>

            </div>

    </form>

    {{-- Dedicated DELETE forms for previously uploaded evidence (HTML5 form attribute link) --}}
    @foreach($incident->evidence as $ev)
        <form id="delete-evidence-{{ $ev->id }}"
            method="POST"
            action="{{ route('citizen.incidents.evidence.destroy', [$incident->id, $ev->id]) }}"
            class="hidden"
            onsubmit="return confirm('Are you sure you want to remove this uploaded evidence file?');">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #incident-map .leaflet-tile-pane {
        filter: invert(1) hue-rotate(180deg) brightness(0.85) saturate(0.9) contrast(0.95);
    }
    #incident-map .leaflet-marker-pane,
    #incident-map .leaflet-popup-pane,
    #incident-map .leaflet-shadow-pane,
    #incident-map .leaflet-overlay-pane {
        filter: invert(1) hue-rotate(180deg);
    }

    .leaflet-popup-content-wrapper {
        background: #0f172a !important;
        color: #f1f5f9 !important;
        border: 1px solid #334155 !important;
        border-radius: 0.75rem !important;
        font-family: 'Inter', sans-serif !important;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5) !important;
    }
    .leaflet-popup-tip { background: #0f172a !important; }
    .leaflet-popup-close-button { color: #64748b !important; }
    .leaflet-popup-close-button:hover { color: #f1f5f9 !important; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const displayLat = document.getElementById('display-lat');
        const displayLng = document.getElementById('display-lng');
        const mapStatus = document.getElementById('map-status');
        const addressInput = document.getElementById('location_address');
        const cityInput = document.getElementById('location_city');

        let defaultLat = parseFloat(latInput.value) || 14.6091;
        let defaultLng = parseFloat(lngInput.value) || 121.0223;

        // Initialize Leaflet Map
        const map = L.map('incident-map', {
            zoomControl: true,
            scrollWheelZoom: false
        }).setView([defaultLat, defaultLng], 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Draggable Marker
        let marker = L.marker([defaultLat, defaultLng], {
            draggable: true,
            title: "Drag to adjust emergency location"
        }).addTo(map);

        marker.bindPopup("<div class='text-xs font-bold text-red-400'>&bull; Emergency Ground Zero</div><div class='text-[10px] text-slate-300'>Drag or click map to move</div>").openPopup();

        function updateCoordinates(lat, lng, reverseGeocode = false) {
            lat = parseFloat(lat.toFixed(7));
            lng = parseFloat(lng.toFixed(7));

            latInput.value = lat;
            lngInput.value = lng;
            displayLat.textContent = lat.toFixed(4);
            displayLng.textContent = lng.toFixed(4);

            marker.setLatLng([lat, lng]);

            if (reverseGeocode) {
                mapStatus.textContent = "Updating street address...";
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`, {
                    headers: { 'Accept-Language': 'en' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data && data.address) {
                        const addr = data.address;
                        const road = addr.road || addr.suburb || addr.neighbourhood || addr.amenity || '';
                        const house = addr.house_number ? `${addr.house_number} ` : '';
                        const streetAddress = `${house}${road}`.trim();
                        const city = addr.city || addr.town || addr.municipality || addr.county || 'Metro Manila';

                        if (streetAddress && !addressInput.value) {
                            addressInput.value = streetAddress;
                        }
                        if (city && !cityInput.value) {
                            cityInput.value = city;
                        }

                        mapStatus.textContent = `Pin updated: ${streetAddress || city}`;
                    } else {
                        mapStatus.textContent = "Pin dropped &bull; Coordinates synced";
                    }
                })
                .catch(() => {
                    mapStatus.textContent = "Pin dropped &bull; Coordinates synced";
                });
            } else {
                mapStatus.textContent = "Pin dropped &bull; Coordinates synced";
            }
        }

        // On map click
        map.on('click', function(e) {
            updateCoordinates(e.latlng.lat, e.latlng.lng, true);
        });

        // On marker drag
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateCoordinates(pos.lat, pos.lng, true);
        });

        // HTML5 Geolocation Button
        const btnGps = document.getElementById('btn-detect-gps');
        const gpsSpinner = document.getElementById('gps-spinner');
        const gpsIcon = document.getElementById('gps-icon');

        if (btnGps) {
            btnGps.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    alert('Geolocation is not supported by your browser.');
                    return;
                }

                gpsSpinner.classList.remove('hidden');
                gpsIcon.classList.add('hidden');
                mapStatus.textContent = "Detecting your GPS position...";

                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        gpsSpinner.classList.add('hidden');
                        gpsIcon.classList.remove('hidden');

                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        map.flyTo([lat, lng], 16, { animate: true, duration: 1.2 });
                        updateCoordinates(lat, lng, true);
                        mapStatus.textContent = "GPS position acquired!";
                    },
                    function(error) {
                        gpsSpinner.classList.add('hidden');
                        gpsIcon.classList.remove('hidden');
                        mapStatus.textContent = "Could not obtain GPS. Click map manually.";
                        console.warn('Geolocation error:', error);
                    },
                    { timeout: 10000, enableHighAccuracy: true }
                );
            });
        }

        // New file input preview and clear handler
        const evidenceInput = document.getElementById('evidence_file');
        const previewContainer = document.getElementById('new-file-preview-container');
        const previewImg = document.getElementById('new-file-preview-img');
        const previewDoc = document.getElementById('new-file-preview-doc');
        const fileNameSpan = document.getElementById('new-file-name');
        const btnClearNew = document.getElementById('btn-clear-new-file');

        if (evidenceInput && previewContainer) {
            evidenceInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) {
                    previewContainer.classList.add('hidden');
                    return;
                }

                fileNameSpan.textContent = file.name;
                previewContainer.classList.remove('hidden');

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        previewImg.src = ev.target.result;
                        previewImg.classList.remove('hidden');
                        previewDoc.classList.add('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewImg.classList.add('hidden');
                    previewDoc.classList.remove('hidden');
                }
            });

            if (btnClearNew) {
                btnClearNew.addEventListener('click', function() {
                    evidenceInput.value = '';
                    previewContainer.classList.add('hidden');
                    previewImg.src = '';
                });
            }
        }

        // Auto-select default threat level when a phenomenon classification is picked
        const incidentTypeSelect = document.getElementById('incident_type_id');
        if (incidentTypeSelect) {
            incidentTypeSelect.addEventListener('change', function() {
                const selectedOption = incidentTypeSelect.options[incidentTypeSelect.selectedIndex];
                const defaultThreat = selectedOption ? selectedOption.getAttribute('data-threat') : null;
                if (defaultThreat) {
                    const radio = document.querySelector(`input[name="threat_level"][value="${defaultThreat}"]`);
                    if (radio) {
                        radio.checked = true;
                    }
                }
            });
        }
    });
</script>
@endpush
