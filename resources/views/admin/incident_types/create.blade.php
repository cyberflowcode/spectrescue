@extends('layouts.app')

@section('title', 'Add Phenomenon Classification')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Top Back Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.incident_types.index') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
            <span>&larr; Back to Classifications</span>
        </a>
        <span class="text-xs font-mono text-slate-400">Tactical Reference &bull; <strong class="text-cyan-400">New Category</strong></span>
    </div>

    {{-- Create Form Card --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
        <div class="border-b border-slate-800 pb-4">
            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Registry Entry</span>
            <h1 class="text-2xl font-bold text-white mt-1">Add Phenomenon Classification</h1>
            <p class="text-xs text-slate-400 mt-1">Define a new paranormal entity manifestation pattern for citizens to report and dispatchers to evaluate.</p>
        </div>

        <form method="POST" action="{{ route('admin.incident_types.store') }}" class="space-y-6">
            @csrf

            {{-- Classification Name --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Classification Name <span class="text-red-400">*</span>
                </label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="e.g. Spectral Doppelganger / Mimic"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                @error('name')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-500">Provide a clear, recognizable designation for this paranormal category.</p>
            </div>

            {{-- Default Threat Level --}}
            <div class="space-y-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Default Threat / Danger Level <span class="text-red-400">*</span>
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-emerald-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                        <input type="radio" name="default_threat_level" value="Low" {{ old('default_threat_level') == 'Low' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500">
                        <span class="text-xs font-medium text-emerald-400">Low</span>
                    </label>
                    <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-yellow-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                        <input type="radio" name="default_threat_level" value="Medium" {{ old('default_threat_level', 'Medium') == 'Medium' ? 'checked' : '' }} class="text-yellow-500 focus:ring-yellow-500">
                        <span class="text-xs font-medium text-yellow-400">Medium</span>
                    </label>
                    <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-orange-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                        <input type="radio" name="default_threat_level" value="High" {{ old('default_threat_level') == 'High' ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500">
                        <span class="text-xs font-medium text-orange-400">High</span>
                    </label>
                    <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-red-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                        <input type="radio" name="default_threat_level" value="Critical" {{ old('default_threat_level') == 'Critical' ? 'checked' : '' }} class="text-red-500 focus:ring-red-500">
                        <span class="text-xs font-medium text-red-400">Critical</span>
                    </label>
                </div>
                @error('default_threat_level')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-500">This danger level is suggested automatically when a citizen selects this classification.</p>
            </div>

            {{-- Description / Typical Symptoms --}}
            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Typical Manifestation Symptoms &amp; Description
                </label>
                <textarea id="description" name="description" rows="4"
                    placeholder="Describe typical signs: disembodied vocalizations, extreme EMF spikes, violent levitation, temperature drop, sulfur scent, psychic residue..."
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-500">Explains symptoms so citizens and dispatchers recognize this phenomenon.</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                <a href="{{ route('admin.incident_types.index') }}"
                    class="px-4 py-2.5 rounded-xl text-xs font-medium text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-lg shadow-cyan-600/30 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Save Classification</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
