@extends('layouts.app')

@section('title', 'Responder Specializations')

@section('content')
<div class="space-y-6">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                <span class="text-xs font-mono uppercase tracking-widest text-cyan-400">Field Operations Directory</span>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Responder Specializations</h1>
            <p class="text-sm text-slate-400 mt-0.5">Manage tactical roles, field divisions, and auto-badge ID prefixes for responder registration.</p>
        </div>

        <div class="flex items-center space-x-2 shrink-0">
            <a href="{{ route('admin.specializations.create') }}"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-cyan-600 hover:bg-cyan-500 text-white shadow-lg shadow-cyan-600/30 transition-all flex items-center space-x-1.5 hover:-translate-y-0.5 transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Specialization</span>
            </a>
        </div>
    </div>

    {{-- Summary Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-mono">Specialization Roles</span>
                <div class="text-2xl font-extrabold text-white mt-1">{{ $specializations->total() }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-mono">Assigned Responders</span>
                <div class="text-2xl font-extrabold text-white mt-1">{{ \App\Models\Responder::count() }}</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-mono">Active In Registration</span>
                <div class="text-2xl font-extrabold text-emerald-400 mt-1">
                    {{ \App\Models\Specialization::where('is_active', true)->count() }}
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-mono">Badge Code Prefixes</span>
                <div class="text-2xl font-extrabold text-purple-400 mt-1">
                    {{ \App\Models\Specialization::distinct('code_prefix')->count('code_prefix') }}
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
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
                        <th class="py-3.5 px-6">Specialization Role</th>
                        <th class="py-3.5 px-6">Badge Prefix</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6">Responders</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-200">
                    @forelse($specializations as $spec)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-6 font-semibold text-white">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                    <span>{{ $spec->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-mono text-xs font-bold text-cyan-300 bg-cyan-500/15 px-2.5 py-1 rounded-md border border-cyan-500/30">
                                    {{ $spec->code_prefix }}-XXXX
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-400 max-w-xs truncate">
                                {{ $spec->description ?: 'No operational description logged.' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                    <svg class="w-3 h-3 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                    <span>{{ $spec->responders_count ?? 0 }}</span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.specializations.edit', $spec) }}"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-colors">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.specializations.destroy', $spec) }}"
                                        onsubmit="return confirm('Are you sure you want to delete specialization \'{{ $spec->name }}\'?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-red-950/40 hover:bg-red-900/60 text-red-300 border border-red-800/40 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500 font-mono text-xs">
                                No specializations registered yet. Click &quot;Add Specialization&quot; to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Card List --}}
        <div class="md:hidden divide-y divide-slate-800">
            @forelse($specializations as $spec)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h3 class="text-sm font-bold text-white">{{ $spec->name }}</h3>
                            <span class="inline-block mt-1 font-mono text-xs font-bold text-cyan-300 bg-cyan-500/15 px-2 py-0.5 rounded border border-cyan-500/30">
                                Prefix: {{ $spec->code_prefix }}
                            </span>
                        </div>
                        <span class="text-xs font-mono text-slate-400 bg-slate-800 px-2 py-1 rounded border border-slate-700">
                            {{ $spec->responders_count ?? 0 }} responders
                        </span>
                    </div>

                    @if($spec->description)
                        <p class="text-xs text-slate-400">{{ $spec->description }}</p>
                    @endif

                    <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-800/50">
                        <a href="{{ route('admin.specializations.edit', $spec) }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 text-slate-200 border border-slate-700">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('admin.specializations.destroy', $spec) }}"
                            onsubmit="return confirm('Are you sure you want to delete specialization \'{{ $spec->name }}\'?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-950/40 text-red-300 border border-red-800/40">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-500 font-mono text-xs">
                    No specializations registered yet.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($specializations->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/50">
                {{ $specializations->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
