@extends('layouts.app')

@section('title', 'Edit Classification - ' . $incidentType->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Top Back Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.incident_types.index') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
            <span>&larr; Back to Classifications</span>
        </a>
        <span class="text-xs font-mono text-slate-400">Tactical Reference &bull; <strong class="text-cyan-400">Edit #{{ $incidentType->id }}</strong></span>
    </div>

    {{-- Info Card if incidents exist --}}
    @if($incidentType->incidents_count > 0)
    <div class="p-4 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-start space-x-3 text-xs text-indigo-300">
        <svg class="w-5 h-5 text-indigo-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <span class="font-bold text-white">Active Association:</span>
            There are currently <strong class="text-white">{{ $incidentType->incidents_count }}</strong> emergency incident(s) categorized under this classification. Updating the name or default threat level will reflect across future reports and summaries.
        </div>
    </div>
    @endif

    {{-- Edit Form Card --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
        <div class="border-b border-slate-800 pb-4 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Edit Classification</span>
                <h1 class="text-2xl font-bold text-white mt-1">{{ $incidentType->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Modify manifestation symptoms and default danger categorization.</p>
            </div>
            <span class="text-xs font-mono text-slate-500 bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800">
                ID: #{{ $incidentType->id }}
            </span>
        </div>

        <form method="POST" action="{{ route('admin.incident_types.update', $incidentType) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Classification Name --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Classification Name <span class="text-red-400">*</span>
                </label>
                <input id="name" type="text" name="name" value="{{ old('name', $incidentType->name) }}" required autofocus
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                @error('name')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Default Threat Level --}}
            <div class="space-y-2">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Default Threat / Danger Level <span class="text-red-400">*</span>
                </label>
                @php $currentThreat = old('default_threat_level', $incidentType->default_threat_level); @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-emerald-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                        <input type="radio" name="default_threat_level" value="Low" {{ $currentThreat == 'Low' ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500">
                        <span class="text-xs font-medium text-emerald-400">Low</span>
                    </label>
                    <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-yellow-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                        <input type="radio" name="default_threat_level" value="Medium" {{ $currentThreat == 'Medium' ? 'checked' : '' }} class="text-yellow-500 focus:ring-yellow-500">
                        <span class="text-xs font-medium text-yellow-400">Medium</span>
                    </label>
                    <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-orange-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                        <input type="radio" name="default_threat_level" value="High" {{ $currentThreat == 'High' ? 'checked' : '' }} class="text-orange-500 focus:ring-orange-500">
                        <span class="text-xs font-medium text-orange-400">High</span>
                    </label>
                    <label class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-red-500/50 cursor-pointer flex items-center space-x-2 transition-colors">
                        <input type="radio" name="default_threat_level" value="Critical" {{ $currentThreat == 'Critical' ? 'checked' : '' }} class="text-red-500 focus:ring-red-500">
                        <span class="text-xs font-medium text-red-400">Critical</span>
                    </label>
                </div>
                @error('default_threat_level')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description / Typical Symptoms --}}
            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Typical Manifestation Symptoms &amp; Description
                </label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">{{ old('description', $incidentType->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                <div>
                    {{-- Delete trigger --}}
                    @if($incidentType->incidents_count === 0)
                        <button type="button"
                            onclick="if(confirm('Are you sure you want to delete this classification?')) { document.getElementById('delete-type-form').submit(); }"
                            class="text-xs text-red-400 hover:text-red-300 hover:underline transition-colors flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Delete Classification</span>
                        </button>
                    @else
                        <span class="text-[11px] text-slate-500 font-mono" title="Cannot delete while incidents are assigned">
                            Protected ({{ $incidentType->incidents_count }} linked)
                        </span>
                    @endif
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.incident_types.index') }}"
                        class="px-4 py-2.5 rounded-xl text-xs font-medium text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 shadow-lg shadow-cyan-600/30 transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Update Classification</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Hidden delete form if zero incidents --}}
        @if($incidentType->incidents_count === 0)
        <form id="delete-type-form" method="POST" action="{{ route('admin.incident_types.destroy', $incidentType) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
        @endif
    </div>

</div>
@endsection
