@extends('layouts.app')

@section('title', 'Report Paranormal Incident')

@section('content')
<div class="w-full space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('citizen.dashboard') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
            <span>&larr; Back to Incident Log</span>
        </a>
        <span class="text-xs uppercase px-3 py-1 rounded-full bg-red-500/15 text-red-400 font-mono border border-red-500/30 font-bold flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-red-400 animate-ping"></span>
            <span>Emergency Transmission Portal</span>
        </span>
    </div>

    <div class="border-b border-slate-800 pb-4">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Report Paranormal Emergency</h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">Provide clear, accurate details of the activity so dispatchers can assign the correct field exorcist or ghost hunter.</p>
    </div>

    <form method="POST" action="{{ route('citizen.incidents.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- 2 Column Grid for Desktop --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

            {{-- LEFT COLUMN: Incident Form --}}
            <div class="lg:col-span-6 space-y-6 bg-slate-900 border border-slate-800 rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl shadow-slate-950/50">

                <div class="flex items-center space-x-2.5 pb-3 border-b border-slate-800">
                    <div class="w-3 h-3 rounded-full bg-cyan-400"></div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider font-mono">1. Emergency Incident Details</h2>
                </div>

                <!-- Incident Type -->
                <div class="space-y-1.5">
                    <label for="incident_type_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Phenomenon Classification <span class="text-red-400">*</span>
                    </label>
                    <select id="incident_type_id" name="incident_type_id" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                        <option value="">Select Category...</option>
                        @foreach($incidentTypes as $type)
                            <option value="{{ $type->id }}" data-threat="{{ $type->default_threat_level }}" {{ old('incident_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} (Default Threat: {{ $type->default_threat_level }})
                            </option>
                        @endforeach
                    </select>
                    <span class="block text-[11px] text-slate-500">Choose the closest manifestation pattern occurring at your location.</span>
                </div>

                <!-- Title -->
                <div class="space-y-1.5">
                    <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Emergency Headline / Title <span class="text-red-400">*</span>
                    </label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Violent Kitchen Poltergeist and Cold Spots" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                </div>

                <!-- Threat Level -->
                <div class="space-y-1.5">
                    <label for="threat_level" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Perceived Threat Level <span class="text-red-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-emerald-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                            <input type="radio" name="threat_level" value="Low" {{ old('threat_level') == 'Low' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500">
                            <span class="text-xs font-medium text-emerald-400">Low</span>
                        </label>
                        <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-yellow-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                            <input type="radio" name="threat_level" value="Medium" {{ old('threat_level', 'Medium') == 'Medium' ? 'checked' : '' }} class="text-yellow-500 focus:ring-yellow-500">
                            <span class="text-xs font-medium text-yellow-400">Medium</span>
                        </label>
                        <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-orange-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                            <input type="radio" name="threat_level" value="High" {{ old('threat_level') == 'High' ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500">
                            <span class="text-xs font-medium text-orange-400">High</span>
                        </label>
                        <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-red-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                            <input type="radio" name="threat_level" value="Critical" {{ old('threat_level') == 'Critical' ? 'checked' : '' }} class="text-red-500 focus:ring-red-500">
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
                        <input id="location_address" type="text" name="location_address" value="{{ old('location_address') }}" placeholder="e.g. 13 Balete Drive, New Manila" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                    </div>

                    <div class="space-y-1.5">
                        <label for="location_city" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            City / District <span class="text-red-400">*</span>
                        </label>
                        <input id="location_city" type="text" name="location_city" value="{{ old('location_city') }}" placeholder="e.g. Quezon City" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                    </div>
                </div>

                <!-- Detailed Description -->
                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Detailed Phenomenon Description <span class="text-red-400">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" placeholder="Describe symptoms: flying objects, disembodied whispers, unnatural smells, temperature drop, electrical anomalies, physical marks..." required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">{{ old('description') }}</textarea>
                </div>

                <!-- Evidence / Image Upload -->
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                    <div class="space-y-1.5">
                        <label for="evidence_file" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Upload Evidence / Anomaly Photo (Optional)
                        </label>
                        <div class="flex items-center space-x-2">
                            <input id="evidence_file" type="file" name="evidence_file" accept="image/*,.pdf"
                                class="text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-cyan-400 hover:file:bg-slate-700 cursor-pointer">
                            <button type="button" id="btn-clear-evidence" title="Remove / Change file"
                                class="hidden items-center justify-center w-6 h-6 rounded-full bg-red-500/20 hover:bg-red-500 text-red-400 hover:text-white transition-all text-sm font-bold leading-none cursor-pointer">
                                &times;
                            </button>
                        </div>
                        <span class="block text-[11px] text-slate-500">Supports JPG, PNG, WEBP, or PDF up to 10MB.</span>
                    </div>

                    <div class="space-y-1.5">
                        <label for="evidence_caption" class="block text-xs font-medium text-slate-400">Photo Caption / Note</label>
                        <input id="evidence_caption" type="text" name="evidence_caption" value="{{ old('evidence_caption') }}" placeholder="e.g. Photo of cracked bedroom mirror and cold mist"
                            class="w-full px-3.5 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs">
                    </div>
                </div>

                <!-- Submit Button (Desktop Only; on mobile it appears below the tactical map pin) -->
                <div class="hidden lg:block pt-2">
                    <button type="submit" class="w-full py-3.5 rounded-xl font-bold bg-red-600 hover:bg-red-500 text-white shadow-lg shadow-red-600/30 transition-all flex items-center justify-center space-x-2.5 text-base cursor-pointer">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>Transmit Paranormal Emergency Call &rarr;</span>
                    </button>
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
                                <p class="text-[11px] text-slate-400">Click map or drag marker to set precise coordinates</p>
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

                    {{-- Leaflet Map Canvas --}}
                    <div id="incident-map" class="h-80 sm:h-96 lg:h-[480px] w-full rounded-xl overflow-hidden border border-slate-700 relative z-0"></div>

                    {{-- Coordinates readout & status bar --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] font-mono px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl gap-2">
                        <span id="map-status" class="text-slate-400 truncate">Pin dropped • Coordinates synced</span>
                        <div class="flex items-center space-x-3 text-cyan-400 shrink-0">
                            <span>Lat: <span id="display-lat" class="font-bold">14.6091</span></span>
                            <span class="text-slate-600">•</span>
                            <span>Lng: <span id="display-lng" class="font-bold">121.0223</span></span>
                        </div>
                    </div>

                    {{-- Hidden form inputs for coordinates --}}
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', '14.6091000') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', '121.0223000') }}">

                    {{-- Mobile Submit Button --}}
                    <div class="lg:hidden pt-2">
                        <button type="submit" class="w-full py-3.5 rounded-xl font-bold bg-red-600 hover:bg-red-500 text-white shadow-lg shadow-red-600/30 transition-all flex items-center justify-center space-x-2.5 text-base cursor-pointer">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>Transmit Paranormal Emergency Call &rarr;</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /*
     * Dark map effect via CSS filter — same technique as dispatcher map.
     * OSM tiles are free (no API key). The filter inverts them dark.
     */
    #incident-map .leaflet-tile-pane {
        filter: invert(1) hue-rotate(180deg) brightness(0.85) saturate(0.9) contrast(0.95);
    }
    /* Un-invert the marker and popup so they still look correct */
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

    /* Pulse animation on the draggable marker */
    @keyframes marker-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
        50%       { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
    }
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
        }).setView([defaultLat, defaultLng], 14);

        // 100% Free Official OpenStreetMap Tile Server
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Draggable Emergency Marker
        let marker = L.marker([defaultLat, defaultLng], {
            draggable: true,
            title: "Drag to set emergency location"
        }).addTo(map);

        marker.bindPopup("<div class='text-xs font-bold text-red-400'>&bull; Emergency Ground Zero</div><div class='text-[10px] text-slate-300'>Drag to adjust location</div>").openPopup();

        function updateCoordinates(lat, lng, reverseGeocode = true) {
            lat = parseFloat(lat.toFixed(7));
            lng = parseFloat(lng.toFixed(7));

            latInput.value = lat;
            lngInput.value = lng;
            displayLat.textContent = lat.toFixed(4);
            displayLng.textContent = lng.toFixed(4);

            marker.setLatLng([lat, lng]);

            if (reverseGeocode) {
                mapStatus.textContent = "Fetching street address...";
                // Reverse geocode via OpenStreetMap Nominatim
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
                        } else if (streetAddress && addressInput.value.length < 5) {
                            addressInput.value = streetAddress;
                        }

                        if (city && !cityInput.value) {
                            cityInput.value = city;
                        }

                        mapStatus.textContent = `Location matched: ${streetAddress || city}`;
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

        // Inline clear (✕) button for evidence file input
        const evidenceInput = document.getElementById('evidence_file');
        const btnClear = document.getElementById('btn-clear-evidence');

        if (evidenceInput && btnClear) {
            evidenceInput.addEventListener('change', function() {
                if (evidenceInput.files && evidenceInput.files.length > 0) {
                    btnClear.classList.remove('hidden');
                    btnClear.classList.add('inline-flex');
                } else {
                    btnClear.classList.add('hidden');
                    btnClear.classList.remove('inline-flex');
                }
            });

            btnClear.addEventListener('click', function() {
                evidenceInput.value = '';
                btnClear.classList.add('hidden');
                btnClear.classList.remove('inline-flex');
            });
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

