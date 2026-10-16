@extends('layouts.app')

@section('title', 'Phenomenon Classifications')

@section('content')
<div class="space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-cyan-400">Tactical Reference Data</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Phenomenon Classifications</h1>
            <p class="text-sm text-slate-400 mt-0.5">Manage paranormal threat categories, manifestation descriptions, and default danger levels</p>
        </div>

        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('admin.incident_types.create') }}"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-cyan-600 hover:bg-cyan-500 text-white shadow-lg shadow-cyan-600/30 transition-all flex items-center space-x-1.5 hover:-translate-y-0.5 transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Classification</span>
            </a>
        </div>
    </div>

    {{-- Summary Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-mono">Total Classifications</span>
                <div class="text-2xl font-extrabold text-white mt-1">{{ $incidentTypes->total() }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-mono">Total Linked Incidents</span>
                <div class="text-2xl font-extrabold text-white mt-1">{{ \App\Models\Incident::count() }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-mono">Critical Danger Types</span>
                <div class="text-2xl font-extrabold text-red-400 mt-1">
                    {{ \App\Models\IncidentType::where('default_threat_level', 'Critical')->count() }}
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-mono">High Danger Types</span>
                <div class="text-2xl font-extrabold text-orange-400 mt-1">
                    {{ \App\Models\IncidentType::where('default_threat_level', 'High')->count() }}
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-mono text-xs uppercase">
                        <th class="py-3.5 px-6">Classification Name</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6">Default Threat</th>
                        <th class="py-3.5 px-6">Incidents Logged</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse($incidentTypes as $type)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-6 font-semibold text-white">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-cyan-400 text-xs font-mono font-bold shrink-0">
                                    #{{ $type->id }}
                                </div>
                                <div>
                                    <div class="font-bold text-white">{{ $type->name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">Created {{ $type->created_at ? $type->created_at->format('M d, Y') : 'System Initial' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-slate-400 text-xs max-w-xs leading-relaxed">
                            {{ $type->description ?: 'No detailed symptoms provided.' }}
                        </td>
                        <td class="py-4 px-6">
                            @if($type->default_threat_level === 'Critical')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-red-500/15 text-red-400 border border-red-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-1.5 animate-pulse"></span>
                                    Critical
                                </span>
                            @elseif($type->default_threat_level === 'High')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-orange-500/15 text-orange-400 border border-orange-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 mr-1.5"></span>
                                    High
                                </span>
                            @elseif($type->default_threat_level === 'Medium')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-yellow-500/15 text-yellow-400 border border-yellow-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 mr-1.5"></span>
                                    Medium
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>
                                    Low
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $type->incidents_count }} incident{{ $type->incidents_count == 1 ? '' : 's' }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.incident_types.edit', $type) }}"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-cyan-400 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/30 transition-colors flex items-center space-x-1"
                                    title="Edit Classification">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                    <span>Edit</span>
                                </a>

                                <form method="POST" action="{{ route('admin.incident_types.destroy', $type) }}"
                                    onsubmit="return confirm('Are you sure you want to delete classification \'{{ addslashes($type->name) }}\'? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-red-400 bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 transition-colors flex items-center space-x-1"
                                        title="Delete Classification">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500 text-sm">
                            No phenomenon classifications found. Click "Add Classification" to create one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="md:hidden divide-y divide-slate-800">
            @forelse($incidentTypes as $type)
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="font-bold text-white text-base">{{ $type->name }}</div>
                        <div class="text-[11px] text-slate-500 font-mono">ID #{{ $type->id }}</div>
                    </div>
                    @if($type->default_threat_level === 'Critical')
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold bg-red-500/15 text-red-400 border border-red-500/30">Critical</span>
                    @elseif($type->default_threat_level === 'High')
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold bg-orange-500/15 text-orange-400 border border-orange-500/30">High</span>
                    @elseif($type->default_threat_level === 'Medium')
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold bg-yellow-500/15 text-yellow-400 border border-yellow-500/30">Medium</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">Low</span>
                    @endif
                </div>

                <p class="text-xs text-slate-400 leading-relaxed">
                    {{ $type->description ?: 'No symptoms description.' }}
                </p>

                <div class="flex items-center justify-between pt-2 border-t border-slate-800/80">
                    <span class="text-xs font-mono text-slate-400">
                        {{ $type->incidents_count }} incident{{ $type->incidents_count == 1 ? '' : 's' }}
                    </span>

                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.incident_types.edit', $type) }}"
                            class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-cyan-400 bg-cyan-500/10 border border-cyan-500/30">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.incident_types.destroy', $type) }}"
                            onsubmit="return confirm('Are you sure you want to delete classification \'{{ addslashes($type->name) }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-red-400 bg-red-500/10 border border-red-500/30">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-500 text-sm">
                No phenomenon classifications found.
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($incidentTypes->hasPages())
        <div class="px-6 py-4 border-t border-slate-800 bg-slate-950/40">
            {{ $incidentTypes->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
