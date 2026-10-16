@extends('layouts.app')

@section('title', 'Add Responder Specialization')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Top Back Navigation --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.specializations.index') }}" class="text-xs font-mono text-slate-400 hover:text-white flex items-center space-x-1.5 transition-colors">
            <span>&larr; Back to Specializations</span>
        </a>
        <span class="text-xs font-mono text-slate-400">Personnel Roster &bull; <strong class="text-cyan-400">New Specialization</strong></span>
    </div>

    {{-- Create Form Card --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
        <div class="border-b border-slate-800 pb-4">
            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Personnel Registry</span>
            <h1 class="text-2xl font-bold text-white mt-1">Add Responder Specialization</h1>
            <p class="text-xs text-slate-400 mt-1">Register a new tactical field specialty that applicants can choose when applying as a responder.</p>
        </div>

        <form method="POST" action="{{ route('admin.specializations.store') }}" class="space-y-6">
            @csrf

            {{-- Specialization Name --}}
            <div class="space-y-1.5">
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Specialization Name <span class="text-red-400">*</span>
                </label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="e.g. Bio-Containment &amp; Hazard Specialist"
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                @error('name')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-500">The full public title displayed in the registration dropdown and responder roster.</p>
            </div>

            {{-- Badge Code Prefix --}}
            <div class="space-y-1.5">
                <label for="code_prefix" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Badge Code Prefix <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center space-x-2">
                    <input id="code_prefix" type="text" name="code_prefix" value="{{ old('code_prefix') }}" required maxlength="10"
                        placeholder="e.g. BIO, EXO, HAZ"
                        style="text-transform: uppercase;"
                        oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); document.getElementById('preview-badge').innerText = (this.value || 'PREFIX') + '-XXXX';"
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 font-mono tracking-wider focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">
                    <div class="shrink-0 px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs font-mono text-cyan-400">
                        <span id="preview-badge">{{ old('code_prefix') ? strtoupper(old('code_prefix')) . '-XXXX' : 'PREFIX-XXXX' }}</span>
                    </div>
                </div>
                @error('code_prefix')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-500">2 to 6 letters/digits used as the prefix when auto-generating responder license badge IDs (e.g. <code>EXO-7429</code>).</p>
            </div>

            {{-- Description / Operational Scope --}}
            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                    Operational Scope &amp; Responsibilities
                </label>
                <textarea id="description" name="description" rows="4"
                    placeholder="Describe field responsibilities, required qualifications, or containment equipment used..."
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 text-sm">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-[11px] text-slate-500">Optional summary of duties and protocol standards for this specialization.</p>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-800">
                <a href="{{ route('admin.specializations.index') }}"
                    class="px-4 py-2.5 rounded-xl text-xs font-medium text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold bg-cyan-600 hover:bg-cyan-500 text-white shadow-lg shadow-cyan-600/30 transition-all flex items-center space-x-1.5 hover:-translate-y-0.5 transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Save Specialization</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
