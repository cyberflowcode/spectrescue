@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-indigo-400">Account Management</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Manage Users</h1>
            <p class="text-sm text-slate-400 mt-0.5">View, filter, and manage all registered system accounts</p>
        </div>

        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('admin.users.create') }}"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition-all flex items-center space-x-1.5 hover:-translate-y-0.5 transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add New User</span>
            </a>
    </div>

    {{-- Filter tabs --}}
    <div class="flex flex-wrap gap-2 text-xs font-mono">
            <a href="{{ route('admin.users.index') }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ !request('role') ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                All ({{ \App\Models\User::count() }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'citizen']) }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ request('role') == 'citizen' ? 'bg-slate-600/30 text-slate-200 border-slate-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                Citizens ({{ \App\Models\User::where('role','citizen')->count() }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'responder']) }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ request('role') == 'responder' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                Responders ({{ \App\Models\User::where('role','responder')->count() }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'dispatcher']) }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ request('role') == 'dispatcher' ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                Dispatchers ({{ \App\Models\User::where('role','dispatcher')->count() }})
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'admin']) }}"
                class="px-3 py-1.5 rounded-lg border transition-colors
                {{ request('role') == 'admin' ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white hover:border-slate-600' }}">
                Admins ({{ \App\Models\User::where('role','admin')->count() }})
            </a>
        </div>
    </div>

    {{-- Users Table (desktop) --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-mono text-xs uppercase">
                        <th class="py-3.5 px-6">User</th>
                        <th class="py-3.5 px-6">Contact</th>
                        <th class="py-3.5 px-6">Role</th>
                        <th class="py-3.5 px-6">Profile Info</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Registered</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-800/30 transition-colors {{ !$u->is_active ? 'opacity-60' : '' }}">
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                    @if($u->isAdmin()) bg-indigo-500/20 text-indigo-400 border border-indigo-500/40
                                    @elseif($u->isDispatcher()) bg-amber-500/20 text-amber-400 border border-amber-500/40
                                    @elseif($u->isResponder()) bg-cyan-500/20 text-cyan-400 border border-cyan-500/40
                                    @else bg-slate-700/50 text-slate-300 border border-slate-600/40 @endif">
                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-white">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-mono text-xs text-slate-400">
                            {{ $u->phone ?? '—' }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase
                                @if($u->role === 'admin') bg-indigo-500/20 text-indigo-300 border border-indigo-500/40
                                @elseif($u->role === 'dispatcher') bg-amber-500/20 text-amber-300 border border-amber-500/40
                                @elseif($u->role === 'responder') bg-cyan-500/20 text-cyan-300 border border-cyan-500/40
                                @else bg-slate-800 text-slate-300 border border-slate-700 @endif">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-400">
                            @if($u->isResponder() && $u->responderProfile)
                                <span class="text-cyan-400 font-mono font-bold">{{ $u->responderProfile->badge_number }}</span>
                                <span class="text-slate-500 mx-1">·</span>
                                <span>{{ $u->responderProfile->specialization }}</span>
                                <span class="block text-[10px] mt-0.5
                                    @if($u->responderProfile->verification_status === 'approved') text-emerald-400
                                    @elseif($u->responderProfile->verification_status === 'rejected') text-red-400
                                    @else text-amber-400 @endif">
                                    {{ ucfirst($u->responderProfile->verification_status) }}
                                </span>
                            @else
                                <span class="text-slate-600">Standard Account</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="space-y-1">
                                @if($u->isOnline())
                                    <span class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <span>Online</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span>
                                        <span>Offline</span>
                                    </span>
                                @endif

                                <div>
                                    @if($u->is_active)
                                        <span class="text-[11px] font-mono text-slate-300">Active</span>
                                    @else
                                        <span class="text-[11px] font-mono text-red-400">Deactivated</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-mono text-xs text-slate-500">
                            {{ $u->created_at->format('M d, Y') }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-1.5">
                                <a href="{{ route('admin.users.show', $u->id) }}"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-cyan-400 hover:text-cyan-300 border border-slate-700 transition-colors"
                                    title="View User Dossier">
                                    View
                                </a>
                                <a href="{{ route('admin.users.edit', $u->id) }}"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-amber-400 hover:text-amber-300 border border-slate-700 transition-colors"
                                    title="Edit User Account">
                                    Edit
                                </a>
                                @if(!$u->isAdmin())
                                    <form method="POST" action="{{ route('admin.users.toggle_status', $u->id) }}">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('{{ $u->is_active ? 'Deactivate' : 'Reactivate' }} {{ $u->name }}?');"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors border
                                            {{ $u->is_active
                                                ? 'bg-slate-800 hover:bg-red-500/20 text-slate-400 hover:text-red-300 border-slate-700 hover:border-red-500/40'
                                                : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border-emerald-500/30' }}">
                                            {{ $u->is_active ? 'Deactivate' : 'Reactivate' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-600 font-mono px-1">Protected</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-xs text-slate-500">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-slate-800">
            @forelse($users as $u)
            <div class="p-4 space-y-3 {{ !$u->is_active ? 'opacity-60' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                            @if($u->isAdmin()) bg-indigo-500/20 text-indigo-400 border border-indigo-500/40
                            @elseif($u->isDispatcher()) bg-amber-500/20 text-amber-400 border border-amber-500/40
                            @elseif($u->isResponder()) bg-cyan-500/20 text-cyan-400 border border-cyan-500/40
                            @else bg-slate-700/50 text-slate-300 border border-slate-600/40 @endif">
                            {{ strtoupper(substr($u->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-bold text-white text-sm">{{ $u->name }}</div>
                            <div class="text-xs text-slate-400">{{ $u->email }}</div>
                        </div>
                    </div>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase
                        @if($u->role === 'admin') bg-indigo-500/20 text-indigo-300 border border-indigo-500/40
                        @elseif($u->role === 'dispatcher') bg-amber-500/20 text-amber-300 border border-amber-500/40
                        @elseif($u->role === 'responder') bg-cyan-500/20 text-cyan-300 border border-cyan-500/40
                        @else bg-slate-800 text-slate-300 border border-slate-700 @endif">
                        {{ $u->role }}
                    </span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-800/80">
                    <div class="flex items-center space-x-2">
                        @if($u->isOnline())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center space-x-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Online</span>
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                Offline
                            </span>
                        @endif

                        @if($u->is_active)
                            <span class="text-xs text-slate-300 font-mono">Active</span>
                        @else
                            <span class="text-xs text-red-400 font-mono">Deactivated</span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.users.show', $u->id) }}"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 text-cyan-400 border border-slate-700">
                            View
                        </a>
                        <a href="{{ route('admin.users.edit', $u->id) }}"
                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 text-amber-400 border border-slate-700">
                            Edit
                        </a>
                        @if(!$u->isAdmin())
                            <form method="POST" action="{{ route('admin.users.toggle_status', $u->id) }}">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('{{ $u->is_active ? 'Deactivate' : 'Reactivate' }} {{ $u->name }}?');"
                                    class="px-2.5 py-1 rounded-lg text-xs font-medium border transition-colors
                                    {{ $u->is_active
                                        ? 'bg-slate-800 text-red-400 border-slate-700'
                                        : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' }}">
                                    {{ $u->is_active ? 'Deactivate' : 'Reactivate' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-10 text-center text-xs text-slate-500">No users found.</div>
            @endforelse
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-800">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
