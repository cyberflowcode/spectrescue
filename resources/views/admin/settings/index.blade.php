@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <div class="flex items-center space-x-2 mb-1">
            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
            <span class="text-xs font-mono uppercase tracking-widest text-slate-400">Configuration</span>
        </div>
        <h1 class="text-2xl font-extrabold text-white">System Settings</h1>
        <p class="text-sm text-slate-400 mt-0.5">Manage system-wide information, contact details, and operational parameters</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- === SETTINGS FORM === --}}
        <div class="lg:col-span-2 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl overflow-hidden">

            {{-- Success Flash --}}
            @if(session('success'))
            <div class="alert-animate px-6 py-3 bg-emerald-950/40 border-b border-emerald-500/30 flex items-center space-x-2 text-sm text-emerald-300">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update') }}" class="divide-y divide-slate-800">
                @csrf

                {{-- Section: General --}}
                <div class="px-6 py-5 space-y-4">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                        <span class="text-xs font-mono uppercase text-indigo-400 font-bold">General Information</span>
                    </div>

                    @foreach(['system_name', 'system_tagline'] as $key)
                        @if(isset($settings[$key]))
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300">
                                {{ $settings[$key]->label }}
                            </label>
                            <input type="text" name="settings[{{ $key }}]"
                                value="{{ old('settings.'.$key, $settings[$key]->value) }}"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                                focus:outline-none focus:border-indigo-500 transition-colors">
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- Section: Contact --}}
                <div class="px-6 py-5 space-y-4">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                        <span class="text-xs font-mono uppercase text-cyan-400 font-bold">Contact & Location</span>
                    </div>

                    @foreach(['contact_email', 'emergency_hotline', 'operating_hours', 'hq_address'] as $key)
                        @if(isset($settings[$key]))
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-300">
                                {{ $settings[$key]->label }}
                            </label>
                            <input type="{{ $settings[$key]->type === 'email' ? 'email' : 'text' }}"
                                name="settings[{{ $key }}]"
                                value="{{ old('settings.'.$key, $settings[$key]->value) }}"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                                focus:outline-none focus:border-cyan-500 transition-colors">
                        </div>
                        @endif
                    @endforeach
                </div>

                {{-- Section: Operations --}}
                <div class="px-6 py-5 space-y-4">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span class="text-xs font-mono uppercase text-amber-400 font-bold">Operations & Alerts</span>
                    </div>

                    @if(isset($settings['maintenance_notice']))
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300">
                            {{ $settings['maintenance_notice']->label }}
                            <span class="text-slate-500 font-normal">(leave blank to hide)</span>
                        </label>
                        <textarea name="settings[maintenance_notice]" rows="3"
                            placeholder="e.g. System will be under maintenance on Oct 1, 2026 from 2AM–4AM. Submissions may be delayed."
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm
                            placeholder-slate-600 focus:outline-none focus:border-amber-500 transition-colors resize-none">{{ old('settings.maintenance_notice', $settings['maintenance_notice']->value) }}</textarea>
                        <p class="text-[11px] text-slate-500">If set, this notice will appear as a warning banner on all dashboards.</p>
                    </div>
                    @endif
                </div>

                {{-- Submit --}}
                <div class="px-6 py-5">
                    <button type="submit"
                        class="w-full py-2.5 rounded-xl font-bold text-sm bg-indigo-600 hover:bg-indigo-500 text-white
                        shadow-lg shadow-indigo-600/25 transition-all hover:-translate-y-0.5 transform">
                        Save Settings →
                    </button>
                </div>
            </form>
        </div>

        {{-- === INFO PANEL === --}}
        <div class="space-y-4">

            {{-- Current Values Preview --}}
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 space-y-4">
                <h3 class="font-bold text-white text-sm flex items-center space-x-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Current Values</span>
                </h3>
                <dl class="space-y-3 text-xs">
                    @foreach($settings as $key => $s)
                    <div>
                        <dt class="text-[10px] font-mono text-slate-500 uppercase">{{ $s->label }}</dt>
                        <dd class="text-slate-200 font-medium mt-0.5 leading-relaxed">
                            {{ $s->value ?: '—' }}
                        </dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            {{-- Tip --}}
            <div class="rounded-2xl bg-amber-950/20 border border-amber-500/20 p-4 space-y-2">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs font-semibold text-amber-300">Defense Note</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Settings are stored in the <code class="text-amber-300 font-mono">system_settings</code> database table as key-value pairs. The admin can update them here anytime without touching the source code.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
