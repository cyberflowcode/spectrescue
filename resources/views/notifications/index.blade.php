@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Notifications</h1>
            <p class="text-xs text-slate-400 mt-0.5">System alerts, dispatch updates, and mission notices</p>
        </div>
        @if($notifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read_all') }}">
                @csrf
                <button type="submit"
                    class="px-3 py-1.5 rounded-lg text-xs font-mono bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                    Mark All Read
                </button>
            </form>
        @endif
    </div>

    {{-- Notification List --}}
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl divide-y divide-slate-800">
        @forelse($notifications as $notif)
            <div class="p-4 sm:p-5 flex items-start gap-4 transition-colors
                {{ $notif->is_read ? 'bg-slate-900' : 'bg-indigo-950/20 hover:bg-indigo-950/30' }}">

                {{-- Icon --}}
                <div class="shrink-0 mt-0.5">
                    @if(!$notif->is_read)
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 block mt-1.5"></span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-700 block mt-1.5"></span>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0 space-y-1">
                    <h3 class="font-semibold text-sm {{ $notif->is_read ? 'text-slate-300' : 'text-white' }}">
                        {{ $notif->title }}
                    </h3>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $notif->message }}</p>
                    <span class="text-[11px] font-mono text-slate-600">{{ $notif->created_at->diffForHumans() }}</span>
                </div>

                {{-- Action --}}
                <div class="shrink-0">
                    @if($notif->link)
                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                            @csrf
                            <button type="submit"
                                class="px-3 py-1.5 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition-colors">
                                Open →
                            </button>
                        </form>
                    @elseif(!$notif->is_read)
                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                            @csrf
                            <button type="submit" title="Mark as read"
                                class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-xs transition-colors border border-slate-700">
                                ✓
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-14 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-400">No notifications yet.</p>
                <p class="text-xs text-slate-600 mt-1">Dispatch updates and alerts will appear here.</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div>{{ $notifications->links() }}</div>
    @endif

</div>
@endsection
