@extends('layouts.app')

@section('title', 'Submit Containment Report — ' . $incident->tracking_code)

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('responder.dashboard') }}"
            class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span>Back to Dashboard</span>
        </a>
        <span class="text-xs uppercase px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-400 font-mono border border-emerald-500/30">
            Resolution Protocol
        </span>
    </div>

    {{-- Incident Banner --}}
    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 border border-cyan-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="font-mono text-xs text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->threatBadgeClass() }}">{{ $incident->threat_level }}</span>
            </div>
            <h2 class="font-bold text-white text-base">{{ $incident->title }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">📍 {{ $incident->location_address }}, {{ $incident->location_city }}</p>
        </div>
        <div class="text-xs text-slate-400 shrink-0">
            <span class="text-slate-500 block text-[10px] font-mono">FILED</span>
            {{ $incident->created_at->format('M d, Y') }}
        </div>
    </div>

    {{-- Report Form --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 shadow-xl space-y-6">
        <div>
            <h1 class="text-xl font-extrabold text-white">Incident Containment Report</h1>
            <p class="text-xs text-slate-400 mt-1">Fill in your field findings to officially close and resolve this emergency.</p>
        </div>

        <form method="POST" action="{{ route('responder.reports.store', $incident->id) }}" class="space-y-5">
            @csrf

            {{-- Containment Method --}}
            <div class="space-y-1.5">
                <label for="containment_method" class="block text-xs font-semibold text-slate-300">
                    Containment & Neutralization Method <span class="text-red-400">*</span>
                </label>
                <select id="containment_method" name="containment_method" required
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                    focus:outline-none focus:border-cyan-500 transition-colors">
                    <option value="">Select primary method…</option>
                    @foreach([
                        'Holy Water & Sacred Roman Exorcism Rite',
                        'Salt Ring Boundary & Iron Filings Array',
                        'High-Intensity EMF Surge & Ionization Discharge',
                        'Spiritual Relic Isolation & Banishment Rune',
                        'Astral Pacification & Seance Resolution',
                        'Sanctification Ritual & Sage Cleansing',
                    ] as $method)
                        <option value="{{ $method }}" {{ old('containment_method') == $method ? 'selected' : '' }}>
                            {{ $method }}
                        </option>
                    @endforeach
                </select>
                @error('containment_method')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            {{-- Threat Neutralized --}}
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-300">
                    Was the Paranormal Threat Fully Neutralized? <span class="text-red-400">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center space-x-3 p-3.5 rounded-xl bg-slate-950 border cursor-pointer
                        border-slate-800 hover:border-emerald-500/50 transition-colors has-[:checked]:border-emerald-500/50 has-[:checked]:bg-emerald-950/20">
                        <input type="radio" name="threat_neutralized" value="1"
                            {{ old('threat_neutralized', '1') == '1' ? 'checked' : '' }}
                            class="text-emerald-500 focus:ring-emerald-500 focus:ring-1">
                        <span class="text-xs font-semibold text-emerald-400">✓ Fully Neutralized</span>
                    </label>
                    <label class="flex items-center space-x-3 p-3.5 rounded-xl bg-slate-950 border cursor-pointer
                        border-slate-800 hover:border-amber-500/50 transition-colors has-[:checked]:border-amber-500/50 has-[:checked]:bg-amber-950/20">
                        <input type="radio" name="threat_neutralized" value="0"
                            {{ old('threat_neutralized') == '0' ? 'checked' : '' }}
                            class="text-amber-500 focus:ring-amber-500 focus:ring-1">
                        <span class="text-xs font-semibold text-amber-400">⚠ Partially Contained</span>
                    </label>
                </div>
                @error('threat_neutralized')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            {{-- Findings --}}
            <div class="space-y-1.5">
                <label for="findings_summary" class="block text-xs font-semibold text-slate-300">
                    Field Findings & Entity Analysis <span class="text-red-400">*</span>
                </label>
                <textarea id="findings_summary" name="findings_summary" rows="5" required
                    placeholder="Describe the entity encountered, the origin of the disturbance, how the containment was executed, and the final outcome…"
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                    placeholder-slate-600 focus:outline-none focus:border-cyan-500 transition-colors resize-none">{{ old('findings_summary') }}</textarea>
                @error('findings_summary')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
            </div>

            {{-- Evidence + Recommendations row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="evidence_collected" class="block text-xs font-semibold text-slate-300">
                        Evidence Collected <span class="text-slate-500 font-normal">(optional)</span>
                    </label>
                    <input id="evidence_collected" type="text" name="evidence_collected"
                        value="{{ old('evidence_collected') }}"
                        placeholder="e.g. EMF 64 mG, ectoplasm sample…"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                        placeholder-slate-600 focus:outline-none focus:border-cyan-500 transition-colors">
                    @error('evidence_collected')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label for="recommendations" class="block text-xs font-semibold text-slate-300">
                        Safety Directives <span class="text-slate-500 font-normal">(optional)</span>
                    </label>
                    <input id="recommendations" type="text" name="recommendations"
                        value="{{ old('recommendations') }}"
                        placeholder="e.g. Keep salt lines for 72 hrs…"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                        placeholder-slate-600 focus:outline-none focus:border-cyan-500 transition-colors">
                    @error('recommendations')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-2 border-t border-slate-800">
                <button type="submit"
                    class="w-full py-3 rounded-xl font-bold text-sm bg-emerald-500 hover:bg-emerald-400 text-slate-950
                    shadow-lg shadow-emerald-500/25 transition-all hover:-translate-y-0.5 transform flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Submit Report & Mark Resolved</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
