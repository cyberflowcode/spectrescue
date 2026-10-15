@extends('layouts.app')

@section('title', 'Send Notifications')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <div class="flex items-center space-x-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
            <span class="text-xs font-mono uppercase tracking-widest text-indigo-400">Admin Broadcast</span>
        </div>
        <h1 class="text-2xl font-extrabold text-white">Send In-App Notifications</h1>
        <p class="text-sm text-slate-400 mt-0.5">Broadcast system alerts and announcements to users by role or individually</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- === COMPOSE FORM === --}}
        <div class="lg:col-span-2 rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-5 shadow-xl">
            <div class="flex items-center space-x-2 pb-4 border-b border-slate-800">
                <div class="w-8 h-8 rounded-lg bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h2 class="font-bold text-white text-base">Compose Broadcast</h2>
            </div>

            <form method="POST" action="{{ route('admin.notifications.send') }}" class="space-y-5" id="notify-form">
                @csrf

                {{-- Notification Title --}}
                <div class="space-y-1.5">
                    <label for="title" class="block text-xs font-semibold text-slate-300">
                        Notification Title <span class="text-red-400">*</span>
                    </label>
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required
                        maxlength="100" placeholder="e.g. System Maintenance on Oct 1"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border text-white text-sm placeholder-slate-600
                        focus:outline-none transition-colors
                        {{ $errors->has('title') ? 'border-red-500' : 'border-slate-700 focus:border-indigo-500' }}">
                    @error('title')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Message Body --}}
                <div class="space-y-1.5">
                    <label for="message" class="block text-xs font-semibold text-slate-300">
                        Message <span class="text-red-400">*</span>
                    </label>
                    <textarea id="message" name="message" rows="4" required maxlength="500"
                        placeholder="Write your announcement or alert message here..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                        placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors resize-none">{{ old('message') }}</textarea>
                    @error('message')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    <p class="text-[11px] text-slate-500 text-right">Max 500 characters</p>
                </div>

                {{-- Optional Link --}}
                <div class="space-y-1.5">
                    <label for="link" class="block text-xs font-semibold text-slate-300">
                        Action Link <span class="text-slate-500 font-normal">(optional — users click "Open →")</span>
                    </label>
                    <input id="link" type="url" name="link" value="{{ old('link') }}"
                        placeholder="https://..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                        placeholder-slate-600 focus:outline-none focus:border-indigo-500 transition-colors">
                    @error('link')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Target Selector --}}
                <div class="space-y-3">
                    <label class="block text-xs font-semibold text-slate-300">
                        Send To <span class="text-red-400">*</span>
                    </label>

                    {{-- Target radio buttons --}}
                    <div class="grid grid-cols-3 gap-3">
                        <label class="flex flex-col items-center p-3 rounded-xl bg-slate-950 border cursor-pointer
                            border-slate-800 hover:border-indigo-500/50 transition-colors has-[:checked]:border-indigo-500/60 has-[:checked]:bg-indigo-950/20">
                            <input type="radio" name="target" value="all" {{ old('target','all') == 'all' ? 'checked' : '' }}
                                class="sr-only" onchange="updateTargetUI(this.value)">
                            <svg class="w-6 h-6 text-indigo-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-xs font-bold text-white">All Users</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">{{ $userCount }} total</span>
                        </label>

                        <label class="flex flex-col items-center p-3 rounded-xl bg-slate-950 border cursor-pointer
                            border-slate-800 hover:border-amber-500/50 transition-colors has-[:checked]:border-amber-500/60 has-[:checked]:bg-amber-950/20">
                            <input type="radio" name="target" value="role" {{ old('target') == 'role' ? 'checked' : '' }}
                                class="sr-only" onchange="updateTargetUI(this.value)">
                            <svg class="w-6 h-6 text-amber-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span class="text-xs font-bold text-white">By Role</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Select group</span>
                        </label>

                        <label class="flex flex-col items-center p-3 rounded-xl bg-slate-950 border cursor-pointer
                            border-slate-800 hover:border-cyan-500/50 transition-colors has-[:checked]:border-cyan-500/60 has-[:checked]:bg-cyan-950/20">
                            <input type="radio" name="target" value="user" {{ old('target') == 'user' ? 'checked' : '' }}
                                class="sr-only" onchange="updateTargetUI(this.value)">
                            <svg class="w-6 h-6 text-cyan-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-xs font-bold text-white">Specific User</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">One person</span>
                        </label>
                    </div>

                    {{-- Role selector (shown when target=role) --}}
                    <div id="role-picker" class="{{ old('target') == 'role' ? '' : 'hidden' }} space-y-1.5">
                        <label for="role" class="block text-xs font-semibold text-slate-300">Select Role Group</label>
                        <select id="role" name="role"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm focus:outline-none focus:border-amber-500 transition-colors">
                            <option value="">Choose role...</option>
                            @foreach($roles as $r)
                                <option value="{{ $r }}" {{ old('role') == $r ? 'selected' : '' }}>
                                    {{ ucfirst($r) }}s
                                </option>
                            @endforeach
                        </select>
                        @error('role')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>

                    {{-- User selector (shown when target=user) --}}
                    <div id="user-picker" class="{{ old('target') == 'user' ? '' : 'hidden' }} space-y-1.5">
                        <label for="user_id" class="block text-xs font-semibold text-slate-300">Select User</label>
                        <select id="user_id" name="user_id"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm focus:outline-none focus:border-cyan-500 transition-colors">
                            <option value="">Choose user...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->role }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')<p class="text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-2 border-t border-slate-800">
                    <button type="submit"
                        class="w-full py-2.5 rounded-xl font-bold text-sm bg-indigo-600 hover:bg-indigo-500 text-white
                        shadow-lg shadow-indigo-600/25 transition-all hover:-translate-y-0.5 transform flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Send Notification →</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- === QUICK STATS === --}}
        <div class="space-y-4">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 space-y-4">
                <h3 class="font-bold text-white text-sm">Audience Overview</h3>
                <div class="space-y-3 text-sm">
                    @foreach(['citizen','dispatcher','responder','admin'] as $r)
                        @php $cnt = \App\Models\User::where('role',$r)->count(); @endphp
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono capitalize text-slate-400">{{ $r }}s</span>
                            <span class="font-bold text-white font-mono text-sm">{{ $cnt }}</span>
                        </div>
                    @endforeach
                    <div class="pt-2 border-t border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-mono text-slate-400">Total Users</span>
                        <span class="font-extrabold text-indigo-300 font-mono text-base">{{ $userCount }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-indigo-950/20 border border-indigo-500/20 p-4 space-y-2">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs font-semibold text-indigo-300">How It Works</span>
                </div>
                <ul class="text-xs text-slate-400 space-y-1.5 leading-relaxed">
                    <li>• Notifications appear in each user's <strong class="text-slate-300">bell icon</strong></li>
                    <li>• Admin broadcasts are tagged <strong class="text-indigo-300">[Admin]</strong> in the title</li>
                    <li>• Users can mark them as read from their notifications page</li>
                    <li>• Optional link adds an "Open →" button</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- === SENT HISTORY === --}}
    @if($sentNotifications->count() > 0)
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="font-bold text-white text-base">Recent Broadcasts</h3>
            <span class="text-xs font-mono text-slate-500">Last 20 sent</span>
        </div>
        <div class="divide-y divide-slate-800">
            @foreach($sentNotifications as $notif)
            <div class="px-6 py-3.5 flex items-start justify-between gap-4">
                <div class="space-y-0.5 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ $notif->title }}</p>
                    <p class="text-xs text-slate-400 leading-relaxed line-clamp-1">{{ $notif->message }}</p>
                </div>
                <div class="shrink-0 text-right space-y-0.5">
                    <span class="block text-xs font-mono text-slate-500">{{ $notif->created_at->diffForHumans() }}</span>
                    <span class="block text-[10px] font-mono text-slate-600">To: {{ $notif->user->name ?? '—' }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function updateTargetUI(value) {
    document.getElementById('role-picker').classList.toggle('hidden', value !== 'role');
    document.getElementById('user-picker').classList.toggle('hidden', value !== 'user');
}
// Init on page load
document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="target"]:checked');
    if (checked) updateTargetUI(checked.value);
});
</script>
@endpush
