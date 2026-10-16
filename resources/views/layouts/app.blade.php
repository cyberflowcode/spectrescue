<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="SPECTRESCUE - Centralized paranormal emergency dispatch and response management system.">

    <title>@yield('title', 'Paranormal Emergency Dispatch')</title>
    <!-- Favicon / Title Icon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/icon-form/icons-rescue.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('storage/icon-form/icons-rescue.png') }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN for easy local dev) -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-mono, code { font-family: 'JetBrains Mono', monospace; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Glow effects */
        .glow-cyan  { box-shadow: 0 0 20px rgba(6,182,212,0.2); }
        .glow-red   { box-shadow: 0 0 20px rgba(239,68,68,0.25); }
        .glow-indigo{ box-shadow: 0 0 20px rgba(99,102,241,0.2); }
        .glow-amber { box-shadow: 0 0 20px rgba(245,158,11,0.2); }

        /* Sidebar active link glow */
        .sidebar-active {
            background: rgba(99,102,241,0.15);
            border-left: 3px solid #818cf8;
            color: #a5b4fc;
        }
        .sidebar-link {
            border-left: 3px solid transparent;
            transition: all 0.15s;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.05);
            border-left-color: #475569;
            color: #f1f5f9;
        }

        /* Animated pulse dot */
        @keyframes ping-slow { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.6); opacity: 0.4; } }
        .ping-slow { animation: ping-slow 2s ease-in-out infinite; }

        /* Alert slide-in */
        @keyframes slideDown { from { transform: translateY(-8px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .alert-animate { animation: slideDown 0.3s ease-out; }

        /* Mobile drawer overlay */
        .drawer-overlay { background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); }

        /* Admin sidebar width */
        .admin-sidebar { width: 240px; min-width: 240px; }

        /* Full height layout (viewport minus 56px header) */
        .layout-shell { min-height: calc(100vh - 3.5rem); }

        /* Tooltip */
        [data-tooltip]:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 110%;
            top: 50%;
            transform: translateY(-50%);
            background: #1e293b;
            color: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            white-space: nowrap;
            border: 1px solid #334155;
            z-index: 100;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-950 text-slate-100 antialiased selection:bg-cyan-500 selection:text-black">

{{-- ==================== TOP HEADER ==================== --}}
<header class="sticky top-0 z-50 bg-slate-900/95 backdrop-blur-md border-b border-slate-800 h-14 flex items-center">
    <div class="flex items-center justify-between w-full px-4 sm:px-6">

        {{-- Brand Logo --}}
        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
            {{-- Mobile hamburger --}}
            @auth
            <button id="mobile-menu-btn" aria-label="Open menu"
                class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            @endauth

            <a href="{{ route('home') }}" class="flex items-center space-x-2 sm:space-x-2.5 group shrink-0">
                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-cyan-600
                    flex items-center justify-center shadow-lg shadow-cyan-600/20 group-hover:scale-105 transition-transform shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm sm:text-base font-extrabold tracking-wider leading-none">
                        <span class="text-cyan-400">SPECT</span><span class="text-white">RESCUE</span>
                    </div>
                    <div class="hidden xs:block sm:block text-[8px] sm:text-[9px] uppercase tracking-[0.18em] text-slate-500 font-mono mt-0.5">Paranormal Dispatch</div>
                </div>
            </a>

            {{-- Desktop nav for non-admin roles --}}
            @auth
            @if(!Auth::user()->isAdmin())
            <nav class="hidden lg:flex items-center space-x-1 pl-4 border-l border-slate-800 ml-1">
                @if(Auth::user()->isCitizen())
                    <a href="{{ route('citizen.incidents.index') }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ (request()->routeIs('citizen.dashboard') || request()->routeIs('citizen.incidents.*')) && !request()->routeIs('citizen.incidents.create') ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        My Reports
                    </a>
                    <a href="{{ route('citizen.incidents.create') }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors flex items-center space-x-1
                        {{ request()->routeIs('citizen.incidents.create') ? 'bg-red-500/15 text-red-400 border border-red-500/30' : 'text-red-400 hover:text-red-300 hover:bg-red-500/10' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Report Emergency</span>
                    </a>
                @elseif(Auth::user()->isDispatcher())
                    <a href="{{ route('dispatcher.dashboard') }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ request()->routeIs('dispatcher.dashboard') ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        Dispatch Console
                    </a>
                @elseif(Auth::user()->isResponder())
                    <a href="{{ route('responder.dashboard') }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ request()->routeIs('responder.dashboard') ? 'bg-cyan-500/15 text-cyan-400 border border-cyan-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}">
                        Mission Dashboard
                    </a>
                @endif
            </nav>
            @endif
            @endauth
        </div>

        {{-- Right side --}}
        <div class="flex items-center space-x-2">
            @guest
                @if(request()->routeIs('home'))
                    <a href="{{ route('login') }}"
                        class="px-3 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition-colors">
                        Log In
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-1.5 rounded-lg text-sm font-semibold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-colors shadow-sm shadow-cyan-500/30">
                        Register
                    </a>
                    <a href="{{ route('register.responder') }}"
                        class="hidden sm:inline-flex px-3 py-1.5 rounded-lg text-sm font-medium border border-indigo-500/40 text-indigo-300 hover:bg-indigo-500/15 transition-colors">
                        Join Responders
                    </a>
                @endif
            @else
                {{-- Notifications --}}
                @php
                    $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                @endphp
                <a href="{{ route('notifications.index') }}" id="notif-bell"
                    class="relative p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                    title="Notifications">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center
                            rounded-full bg-red-500 text-[9px] font-bold text-white leading-none">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                {{-- User pill --}}
                <div class="flex items-center space-x-2 pl-2 border-l border-slate-800">
                    <span class="hidden md:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase
                        @if(Auth::user()->isAdmin()) bg-indigo-500/20 text-indigo-400 border border-indigo-500/40
                        @elseif(Auth::user()->isDispatcher()) bg-amber-500/20 text-amber-400 border border-amber-500/40
                        @elseif(Auth::user()->isResponder()) bg-cyan-500/20 text-cyan-400 border border-cyan-500/40
                        @else bg-slate-700/50 text-slate-300 border border-slate-600/40
                        @endif">
                        <span class="w-1.5 h-1.5 rounded-full mr-1 ping-slow
                            @if(Auth::user()->isAdmin()) bg-indigo-400
                            @elseif(Auth::user()->isDispatcher()) bg-amber-400
                            @elseif(Auth::user()->isResponder()) bg-cyan-400
                            @else bg-slate-400
                            @endif"></span>
                        {{ Auth::user()->role }}
                    </span>

                    <a href="{{ route('profile.edit') }}"
                        class="text-xs sm:text-sm font-semibold text-slate-200 hover:text-cyan-400 transition-colors max-w-[85px] sm:max-w-[130px] truncate"
                        title="{{ Auth::user()->name }}">
                        {{ Auth::user()->name }}
                    </a>

                    {{-- Desktop-only logout button (Mobile logout is accessed via the mobile menu drawer, avoiding redundancy) --}}
                    <form method="POST" action="{{ route('logout') }}" class="hidden lg:inline-block">
                        @csrf
                        <button type="submit" title="Log Out"
                            class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endguest
        </div>
    </div>
</header>

{{-- ==================== MAIN SHELL ==================== --}}
<div class="flex layout-shell">

    {{-- ===== ADMIN SIDEBAR (desktop) ===== --}}
    @auth
    @if(Auth::user()->isAdmin())
    <aside id="admin-sidebar"
        class="admin-sidebar hidden lg:flex flex-col bg-slate-900 border-r border-slate-800 sticky top-14 h-[calc(100vh-3.5rem)] overflow-y-auto shrink-0">

        {{-- Sidebar Header --}}
        <div class="px-4 py-4 border-b border-slate-800">
            <div class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-lg bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-bold text-white">Admin Panel</div>
                    <div class="text-[10px] text-slate-500 font-mono">HQ Control Center</div>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-3 space-y-0.5">
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-link flex items-center space-x-3 px-4 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : 'text-slate-400' }}">
                <svg class="w-4.5 h-4.5 shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            {{-- Users --}}
            <a href="{{ route('admin.users.index') }}"
                class="sidebar-link flex items-center space-x-3 px-4 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs('admin.users.index') ? 'sidebar-active' : 'text-slate-400' }}">
                <svg class="shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Manage Users</span>
            </a>

            {{-- Responders --}}
            <a href="{{ route('admin.responders.index') }}"
                class="sidebar-link flex items-center justify-between px-4 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs('admin.responders.index') ? 'sidebar-active' : 'text-slate-400' }}">
                <div class="flex items-center space-x-3">
                    <svg class="shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Verify Responders</span>
                </div>
                @php $pendingCount = \App\Models\Responder::where('verification_status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.5 rounded-full bg-amber-500 text-[9px] font-bold text-slate-950 font-mono">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            {{-- Incidents --}}
            <a href="{{ route('admin.incidents.index') }}"
                class="sidebar-link flex items-center space-x-3 px-4 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs('admin.incidents.index') ? 'sidebar-active' : 'text-slate-400' }}">
                <svg class="shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span>Manage Incidents</span>
            </a>

            {{-- Phenomenon Classifications --}}
            <a href="{{ route('admin.incident_types.index') }}"
                class="sidebar-link flex items-center space-x-3 px-4 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs('admin.incident_types.*') ? 'sidebar-active' : 'text-slate-400' }}">
                <svg class="shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <span>Phenomenon Types</span>
            </a>

            {{-- Reports --}}
            <a href="{{ route('admin.reports.index') }}"
                class="sidebar-link flex items-center space-x-3 px-4 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs('admin.reports.index') ? 'sidebar-active' : 'text-slate-400' }}">
                <svg class="shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>System Reports</span>
            </a>

            {{-- In-App Notifications --}}
            <a href="{{ route('admin.notifications.index') }}"
                class="sidebar-link flex items-center space-x-3 px-4 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs('admin.notifications.index') ? 'sidebar-active' : 'text-slate-400' }}">
                <svg class="shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span>Send Notifications</span>
            </a>

            {{-- System Settings --}}
            <a href="{{ route('admin.settings.index') }}"
                class="sidebar-link flex items-center space-x-3 px-4 py-2.5 text-sm font-medium transition-colors
                {{ request()->routeIs('admin.settings.index') ? 'sidebar-active' : 'text-slate-400' }}">
                <svg class="shrink-0" style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>System Settings</span>
            </a>

        </nav>
    </aside>
    @endif
    @endauth

    {{-- ===== MOBILE DRAWER OVERLAY ===== --}}
    @auth
    <div id="mobile-overlay" class="hidden fixed inset-0 z-40 drawer-overlay lg:hidden" onclick="closeMobileMenu()"></div>

    {{-- Mobile Drawer --}}
    <div id="mobile-drawer"
        class="fixed top-0 left-0 bottom-0 z-50 w-72 max-w-[85vw] bg-slate-900 border-r border-slate-800
        transform -translate-x-full transition-transform duration-300 lg:hidden overflow-y-auto flex flex-col justify-between">

        <div>
            {{-- Drawer Header with User Profile --}}
            <div class="flex items-center justify-between p-4 border-b border-slate-800">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-cyan-600 flex items-center justify-center font-bold text-white text-xs shrink-0 shadow-md shadow-cyan-600/30">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</div>
                        <div class="text-[10px] text-cyan-400 font-mono uppercase tracking-wider font-semibold">{{ Auth::user()->role }}</div>
                    </div>
                </div>
                <button onclick="closeMobileMenu()" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        {{-- Drawer Nav --}}
        <nav class="p-3 space-y-1">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.users.index') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Manage Users</span>
                </a>
                <a href="{{ route('admin.responders.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.responders.index') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Verify Responders</span>
                </a>
                <a href="{{ route('admin.incidents.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.incidents.index') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Manage Incidents</span>
                </a>
                <a href="{{ route('admin.incident_types.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.incident_types.*') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span>Phenomenon Types</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.reports.index') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>System Reports</span>
                </a>
                <a href="{{ route('admin.notifications.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.notifications.index') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span>Send Notifications</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('admin.settings.index') ? 'bg-indigo-500/20 text-indigo-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>System Settings</span>
                </a>
            @elseif(Auth::user()->isCitizen())

                <a href="{{ route('citizen.incidents.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ (request()->routeIs('citizen.dashboard') || request()->routeIs('citizen.incidents.*')) && !request()->routeIs('citizen.incidents.create') ? 'bg-cyan-500/20 text-cyan-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>My Reports</span>
                </a>
                <a href="{{ route('citizen.incidents.create') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Report Emergency</span>
                </a>
            @elseif(Auth::user()->isDispatcher())
                <a href="{{ route('dispatcher.dashboard') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('dispatcher.dashboard') ? 'bg-amber-500/20 text-amber-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span>Dispatch Console</span>
                </a>
            @elseif(Auth::user()->isResponder())
                <a href="{{ route('responder.dashboard') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium
                    {{ request()->routeIs('responder.dashboard') ? 'bg-cyan-500/20 text-cyan-300' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Mission Dashboard</span>
                </a>
            @endif

            <div class="pt-2 border-t border-slate-800 mt-2">
                <a href="{{ route('notifications.index') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span>Notifications
                        @if($unreadCount > 0)
                            <span class="ml-1 px-1.5 py-0.5 rounded-full bg-red-500 text-white text-[9px] font-bold">{{ $unreadCount }}</span>
                        @endif
                    </span>
                </a>
                <a href="{{ route('profile.edit') }}" onclick="closeMobileMenu()"
                    class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>My Profile</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 transition-colors">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Log Out</span>
                    </button>
                </form>
            </div>
        </nav>
        </div>
    </div>
    @endauth

    {{-- ===== MAIN CONTENT AREA ===== --}}
    <div class="flex-1 min-w-0 flex flex-col w-full overflow-x-hidden">

        {{-- Flash Messages --}}
        <div class="px-3 sm:px-6 lg:px-8 {{ (session('success') || session('error') || session('info') || $errors->any()) ? 'pt-4 space-y-2' : '' }}" id="flash-container">
            @if(session('success'))
                <div class="alert-animate p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300
                    flex items-center justify-between shadow-lg shadow-emerald-950/30">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white text-xl leading-none ml-4">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-animate p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300
                    flex items-center justify-between shadow-lg shadow-red-950/30">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-white text-xl leading-none ml-4">&times;</button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert-animate p-4 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-300
                    flex items-center justify-between shadow-lg shadow-cyan-950/30">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">{{ session('info') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-cyan-400 hover:text-white text-xl leading-none ml-4">&times;</button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert-animate p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 shadow-lg shadow-red-950/30">
                    <div class="flex items-center space-x-2 font-semibold text-sm mb-2 text-red-400">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Please correct the following errors:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 text-red-200/90 pl-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 flex flex-col px-3 sm:px-6 lg:px-8 py-3.5 sm:py-6 w-full max-w-full overflow-x-hidden">
            @yield('content')
        </main>
    </div>
</div>

{{-- ==================== SCRIPTS ==================== --}}
<script>
    // Mobile menu
    function openMobileMenu() {
        document.getElementById('mobile-drawer').classList.remove('-translate-x-full');
        document.getElementById('mobile-overlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeMobileMenu() {
        document.getElementById('mobile-drawer').classList.add('-translate-x-full');
        document.getElementById('mobile-overlay').classList.add('hidden');
        document.body.style.overflow = '';
    }
    const menuBtn = document.getElementById('mobile-menu-btn');
    if (menuBtn) menuBtn.addEventListener('click', openMobileMenu);

    // Auto-dismiss flash alerts after 6 seconds
    setTimeout(function() {
        const container = document.getElementById('flash-container');
        if (container) {
            container.querySelectorAll('[class*="alert-animate"]').forEach(function(el) {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }
    }, 6000);
</script>

@stack('scripts')
</body>
</html>
