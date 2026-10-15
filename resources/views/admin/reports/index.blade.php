@extends('layouts.app')

@section('title', 'System Reports & Resolution Dossiers')

@section('content')
<div class="space-y-6" id="reports-printable">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-emerald-400">Post-Investigation Records</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Paranormal Resolution Dossiers</h1>
            <p class="text-sm text-slate-400 mt-0.5">Archival logs of verified entity neutralizations, containment methods, and responder findings</p>
        </div>
        <div class="flex items-center space-x-2">
            {{-- Threat filter --}}
            <form method="GET" action="{{ route('admin.reports.index') }}">
                <select name="threat_level" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:border-emerald-500">
                    <option value="">All Threats</option>
                    @foreach(['Critical','High','Medium','Low'] as $t)
                        <option value="{{ $t }}" {{ request('threat_level') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </form>
            {{-- Print --}}
            <button type="button" onclick="window.print()"
                class="px-4 py-2 rounded-xl text-xs font-bold font-mono bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 flex items-center space-x-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Print Report</span>
            </button>
        </div>
    </div>

    {{-- Summary Metrics --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
            <span class="text-xs uppercase tracking-wider font-mono text-slate-400">Total Closed Cases</span>
            <div class="text-4xl font-extrabold font-mono text-white mt-2 leading-none">{{ $stats['total_resolved'] }}</div>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900 border border-emerald-500/30">
            <span class="text-xs uppercase tracking-wider font-mono text-emerald-400">Threats Neutralized</span>
            <div class="text-4xl font-extrabold font-mono text-emerald-300 mt-2 leading-none">{{ $stats['threats_neutralized'] }}</div>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900 border border-red-500/30">
            <span class="text-xs uppercase tracking-wider font-mono text-red-400">Critical Cases Contained</span>
            <div class="text-4xl font-extrabold font-mono text-red-300 mt-2 leading-none">{{ $stats['critical_resolved'] }}</div>
        </div>
    </div>

    {{-- Dossier Cards --}}
    <div class="space-y-4">
        @forelse($responses as $rep)
            @php $incident = $rep->incident; @endphp
            <div class="rounded-2xl bg-slate-900 border border-emerald-500/20 shadow-xl overflow-hidden">

                {{-- Dossier Header --}}
                <div class="px-6 py-4 bg-emerald-950/20 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs text-cyan-400 font-bold">{{ $incident->tracking_code }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-mono {{ $incident->threatBadgeClass() }}">{{ $incident->threat_level }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">Resolved</span>
                        </div>
                        <h2 class="text-base font-bold text-white mt-1">{{ $incident->title }}</h2>
                        <div class="text-xs text-slate-400 mt-0.5">
                            📍 {{ $incident->location_address }}, {{ $incident->location_city }}
                        </div>
                    </div>
                    <div class="text-xs font-mono text-slate-400 shrink-0">
                        📅 {{ $rep->submitted_at ? $rep->submitted_at->format('M d, Y • h:i A') : $rep->created_at->format('M d, Y') }}
                    </div>
                </div>

                {{-- Dossier Body --}}
                <div class="p-6 space-y-4">
                    {{-- Specialist + Containment --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5">
                            <span class="text-slate-500 font-mono uppercase text-[10px] block">Field Specialist</span>
                            <div class="font-bold text-white text-sm">{{ $rep->responder->user->name ?? 'Specialist' }}</div>
                            <div class="text-cyan-400 font-mono text-[11px]">
                                {{ $rep->responder->badge_number }} · {{ $rep->responder->specialization }}
                            </div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-1.5">
                            <span class="text-slate-500 font-mono uppercase text-[10px] block">Containment Protocol</span>
                            <div class="font-bold text-emerald-300 text-sm">{{ $rep->containment_method }}</div>
                            <div class="text-[11px] {{ $rep->threat_neutralized ? 'text-emerald-400' : 'text-amber-400' }}">
                                {{ $rep->threat_neutralized ? '✓ Entity Completely Banished' : '⚠ Partially Contained' }}
                            </div>
                        </div>
                    </div>

                    {{-- Findings --}}
                    <div class="space-y-1.5">
                        <span class="text-xs font-mono uppercase text-slate-400">Field Findings &amp; Entity Analysis</span>
                        <div class="text-sm text-slate-200 bg-slate-950/80 p-4 rounded-xl border border-slate-800 leading-relaxed whitespace-pre-line">
                            {{ $rep->findings_summary }}
                        </div>
                    </div>

                    {{-- Evidence + Recommendations --}}
                    @if($rep->evidence_collected || $rep->recommendations)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        @if($rep->evidence_collected)
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <strong class="text-slate-400 font-mono uppercase text-[10px] block mb-1">Evidence Collected</strong>
                            <span class="text-cyan-300">{{ $rep->evidence_collected }}</span>
                        </div>
                        @endif
                        @if($rep->recommendations)
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <strong class="text-slate-400 font-mono uppercase text-[10px] block mb-1">Safety Directives</strong>
                            <span class="text-slate-300">{{ $rep->recommendations }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-16 text-center rounded-2xl bg-slate-900 border border-slate-800">
                <div class="w-14 h-14 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-400 font-medium">No resolution dossiers logged yet.</p>
                <p class="text-xs text-slate-600 mt-1">Dossiers appear when responders submit reports for resolved incidents.</p>
            </div>
        @endforelse
    </div>

    @if($responses->hasPages())
    <div class="pt-2">
        {{ $responses->links() }}
    </div>
    @endif

</div>

@push('styles')
<style>
@media print {
    header, aside, #mobile-drawer, #mobile-overlay { display: none !important; }
    body { background: white !important; color: black !important; }
    .bg-slate-900, .bg-slate-950 { background: #f8fafc !important; }
    .text-white, .text-slate-200, .text-slate-300 { color: #1e293b !important; }
    .text-slate-400, .text-slate-500 { color: #64748b !important; }
    .border-slate-800 { border-color: #e2e8f0 !important; }
    .rounded-2xl { border-radius: 8px !important; }
}
</style>
@endpush
@endsection
