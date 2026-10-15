@extends('layouts.app')

@section('title', 'Verify Responders')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-indigo-400">Accreditation Management</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Field Responders Directory</h1>
            <p class="text-sm text-slate-400 mt-0.5">Review specialist applications, licenses, and accreditation status</p>
        </div>

        {{-- Status filter tabs --}}
        <div class="flex items-center flex-wrap gap-2 font-mono text-xs">
            <a href="{{ route('admin.responders.index') }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ !request('status') ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                All ({{ \App\Models\Responder::count() }})
            </a>
            <a href="{{ route('admin.responders.index', ['status' => 'pending']) }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ request('status') == 'pending' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                Pending ({{ \App\Models\Responder::where('verification_status','pending')->count() }})
            </a>
            <a href="{{ route('admin.responders.index', ['status' => 'approved']) }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ request('status') == 'approved' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                Approved ({{ \App\Models\Responder::where('verification_status','approved')->count() }})
            </a>
            <a href="{{ route('admin.responders.index', ['status' => 'rejected']) }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ request('status') == 'rejected' ? 'bg-red-500/20 text-red-300 border-red-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                Rejected ({{ \App\Models\Responder::where('verification_status','rejected')->count() }})
            </a>
        </div>
    </div>

    {{-- Table (desktop) --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-mono text-xs uppercase">
                        <th class="py-3.5 px-6">Specialist</th>
                        <th class="py-3.5 px-6">Badge / ID</th>
                        <th class="py-3.5 px-6">Specialization</th>
                        <th class="py-3.5 px-6">Bio Statement</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse($responders as $resp)
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="py-4 px-6">
                            <div class="font-bold text-white">{{ $resp->user->name ?? 'Specialist' }}</div>
                            <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $resp->user->email ?? '' }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $resp->user->phone ?? 'No phone' }}</div>
                        </td>
                        <td class="py-4 px-6 font-mono text-sm font-bold text-cyan-400">
                            {{ $resp->badge_number }}
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-200">
                            {{ $resp->specialization }}
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-400 max-w-xs">
                            <div class="line-clamp-2">{{ $resp->bio ?? 'No statement provided.' }}</div>
                            @if($resp->bio && strlen($resp->bio) > 80)
                                <button type="button"
                                    onclick="document.getElementById('bio-{{ $resp->id }}').classList.toggle('hidden')"
                                    class="text-cyan-400 hover:underline text-[10px] mt-1">
                                    View Full Bio
                                </button>
                                <div id="bio-{{ $resp->id }}" class="hidden mt-1 p-2 rounded-lg bg-slate-800 text-slate-300 text-xs leading-relaxed">
                                    {{ $resp->bio }}
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-mono font-bold
                                @if($resp->verification_status === 'approved') bg-emerald-500/20 text-emerald-300 border border-emerald-500/40
                                @elseif($resp->verification_status === 'rejected') bg-red-500/20 text-red-300 border border-red-500/40
                                @else bg-amber-500/20 text-amber-300 border border-amber-500/40 @endif">
                                {{ ucfirst($resp->verification_status) }}
                            </span>
                            @if($resp->verified_at)
                                <div class="text-[10px] text-slate-600 font-mono mt-1">
                                    {{ $resp->verified_at->format('M d, Y') }}
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                @if($resp->verification_status !== 'approved')
                                    <form method="POST" action="{{ route('admin.responders.verify', $resp->id) }}">
                                        @csrf
                                        <input type="hidden" name="verification_status" value="approved">
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition-all shadow shadow-emerald-500/20">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                @if($resp->verification_status !== 'rejected')
                                    <form method="POST" action="{{ route('admin.responders.verify', $resp->id) }}">
                                        @csrf
                                        <input type="hidden" name="verification_status" value="rejected">
                                        <button type="submit"
                                            onclick="return confirm('Reject {{ $resp->user->name }}\'s application?');"
                                            class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-red-500/20 text-slate-400 hover:text-red-300 border border-slate-700 text-xs transition-colors">
                                            Reject
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-xs text-slate-500">
                            No responders found for this filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-slate-800">
            @forelse($responders as $resp)
            <div class="p-4 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-bold text-white">{{ $resp->user->name ?? 'Specialist' }}</div>
                        <div class="text-xs text-cyan-400 font-mono font-bold mt-0.5">{{ $resp->badge_number }}</div>
                        <div class="text-xs text-slate-400">{{ $resp->specialization }}</div>
                    </div>
                    <span class="shrink-0 inline-flex px-2 py-0.5 rounded-full text-[10px] font-mono font-bold
                        @if($resp->verification_status === 'approved') bg-emerald-500/20 text-emerald-300 border border-emerald-500/40
                        @elseif($resp->verification_status === 'rejected') bg-red-500/20 text-red-300 border border-red-500/40
                        @else bg-amber-500/20 text-amber-300 border border-amber-500/40 @endif">
                        {{ ucfirst($resp->verification_status) }}
                    </span>
                </div>
                @if($resp->bio)
                    <p class="text-xs text-slate-400 bg-slate-800/50 p-2 rounded-lg">{{ Str::limit($resp->bio, 100) }}</p>
                @endif
                <div class="flex space-x-2">
                    @if($resp->verification_status !== 'approved')
                        <form method="POST" action="{{ route('admin.responders.verify', $resp->id) }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="verification_status" value="approved">
                            <button type="submit" class="w-full py-2 rounded-xl text-xs font-bold bg-emerald-500 text-slate-950">
                                Approve
                            </button>
                        </form>
                    @endif
                    @if($resp->verification_status !== 'rejected')
                        <form method="POST" action="{{ route('admin.responders.verify', $resp->id) }}" class="{{ $resp->verification_status !== 'approved' ? 'flex-1' : 'w-full' }}">
                            @csrf
                            <input type="hidden" name="verification_status" value="rejected">
                            <button type="submit"
                                onclick="return confirm('Reject?');"
                                class="w-full py-2 rounded-xl text-xs font-medium bg-slate-800 text-red-400 border border-slate-700">
                                Reject
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-xs text-slate-500">No responders found.</div>
            @endforelse
        </div>

        @if($responders->hasPages())
        <div class="px-6 py-4 border-t border-slate-800">
            {{ $responders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
