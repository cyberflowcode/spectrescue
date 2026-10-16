@extends('layouts.app')

@section('title', 'Manage Incidents')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-indigo-400">Incident Management</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Master Incident Registry</h1>
            <p class="text-sm text-slate-400 mt-0.5">Monitor all emergencies and override incident status as needed</p>
        </div>

        {{-- Filter Controls --}}
        <form method="GET" action="{{ route('admin.incidents.index') }}"
            class="flex flex-wrap items-center gap-2 text-xs font-mono">
            <select name="status" onchange="this.form.submit()"
                class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-indigo-500 text-xs">
                <option value="">All Statuses</option>
                @foreach(['Pending','Dispatched','Responding','Resolved'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
            <select name="threat_level" onchange="this.form.submit()"
                class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-indigo-500 text-xs">
                <option value="">All Threats</option>
                @foreach(['Critical','High','Medium','Low'] as $t)
                    <option value="{{ $t }}" {{ request('threat_level') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            @if(request('status') || request('threat_level'))
                <a href="{{ route('admin.incidents.index') }}"
                    class="px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 border border-red-500/30 hover:bg-red-500/20 transition-colors">
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    {{-- Table (desktop) --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-mono text-xs uppercase">
                        <th class="py-3.5 px-5">Code</th>
                        <th class="py-3.5 px-5">Incident</th>
                        <th class="py-3.5 px-5">Citizen</th>
                        <th class="py-3.5 px-5">Location</th>
                        <th class="py-3.5 px-5">Threat</th>
                        <th class="py-3.5 px-5">Status</th>
                        <th class="py-3.5 px-5">Assigned To</th>
                        <th class="py-3.5 px-5">Override</th>
                        <th class="py-3.5 px-5 text-right">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse($incidents as $incident)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-4 px-5 font-mono text-xs font-bold text-cyan-400 align-middle whitespace-nowrap">
                            {{ $incident->tracking_code }}
                        </td>
                        <td class="py-4 px-5 align-middle">
                            <div class="font-bold text-white text-sm">{{ $incident->title }}</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $incident->incidentType->name ?? 'Unspecified' }}</div>
                        </td>
                        <td class="py-4 px-5 text-xs align-middle">
                            <div class="text-slate-200 font-medium">{{ $incident->citizen->name ?? 'Unknown' }}</div>
                            <div class="text-slate-500 font-mono text-[10px]">{{ $incident->citizen->phone ?? '' }}</div>
                        </td>
                        <td class="py-4 px-5 text-xs text-slate-300 align-middle">
                            <div>{{ Str::limit($incident->location_address, 30) }}</div>
                            <div class="text-slate-500 text-[10px]">{{ $incident->location_city }}</div>
                        </td>
                        <td class="py-4 px-5 align-middle whitespace-nowrap">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-mono font-bold {{ $incident->threatBadgeClass() }}">
                                {{ $incident->threat_level }}
                            </span>
                        </td>
                        <td class="py-4 px-5 align-middle whitespace-nowrap">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-mono font-bold {{ $incident->statusBadgeClass() }}">
                                {{ $incident->status }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-xs align-middle">
                            @if($incident->latestAssignment && $incident->latestAssignment->responder)
                                <div class="text-slate-200 font-medium">{{ $incident->latestAssignment->responder->user->name }}</div>
                                <div class="text-cyan-400 font-mono text-[10px]">{{ $incident->latestAssignment->responder->specialization }}</div>
                            @else
                                <span class="text-slate-600 italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 align-middle">
                            {{-- Admin status override --}}
                            <form method="POST" action="{{ route('admin.incidents.override_status', $incident->id) }}">
                                @csrf
                                <div class="flex items-center gap-1 whitespace-nowrap">
                                    <select name="status"
                                        class="px-2 py-1 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                                        @foreach(['Pending','Dispatched','Responding','Resolved'] as $s)
                                            <option value="{{ $s }}" {{ $incident->status == $s ? 'selected' : '' }}>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                        class="px-2.5 py-1 rounded-lg bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-300 text-xs border border-indigo-500/40 transition-colors font-mono shrink-0">
                                        Set
                                    </button>
                                </div>
                            </form>
                        </td>
                        <td class="py-4 px-5 text-right align-middle whitespace-nowrap">
                            <a href="{{ route('admin.incidents.show', $incident->id) }}"
                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-medium transition-colors">
                                Console →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-10 text-center text-xs text-slate-500">No incidents found matching the filters.</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Mobile / tablet cards --}}
        <div class="lg:hidden divide-y divide-slate-800">
            @forelse($incidents as $incident)
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                            <span class="font-mono text-xs text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->threatBadgeClass() }}">{{ $incident->threat_level }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-mono {{ $incident->statusBadgeClass() }}">{{ $incident->status }}</span>
                        </div>
                        <div class="font-bold text-white mt-1">{{ $incident->title }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $incident->location_address }}, {{ $incident->location_city }}</div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <form method="POST" action="{{ route('admin.incidents.override_status', $incident->id) }}" class="flex items-center space-x-1 flex-1">
                        @csrf
                        <select name="status"
                            class="flex-1 px-2 py-1.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none">
                            @foreach(['Pending','Dispatched','Responding','Resolved'] as $s)
                                <option value="{{ $s }}" {{ $incident->status == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="px-3 py-1.5 rounded-lg bg-indigo-500 text-white text-xs font-bold transition-colors">
                            Override
                        </button>
                    </form>
                    <a href="{{ route('admin.incidents.show', $incident->id) }}"
                        class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-300 border border-slate-700 text-xs transition-colors">
                        View
                    </a>
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-xs text-slate-500">No incidents found.</div>
            @endforelse
        </div>

        @if($incidents->hasPages())
        <div class="px-6 py-4 border-t border-slate-800">
            {{ $incidents->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
