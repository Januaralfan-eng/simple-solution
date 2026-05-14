<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — {{ config('agency.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --sidebar-width: 240px; }
    </style>
</head>
<body class="bg-agency-950 text-agency-100 antialiased min-h-screen" x-data>

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="w-[var(--sidebar-width)] fixed top-0 left-0 bottom-0 flex flex-col z-30 border-r border-agency-800 bg-agency-900">

            {{-- Logo --}}
            <div class="px-5 py-4 border-b border-agency-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-white flex items-center justify-center">
                        <span class="font-bold text-black text-xs">{{ substr(config('agency.name'), 0, 1) }}</span>
                    </div>
                    <span class="font-semibold text-sm text-white">Admin Panel</span>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                @php
                    $navItems = [
                        ['icon' => 'grid', 'label' => 'Dashboard',  'route' => 'admin.dashboard'],
                        ['icon' => 'file', 'label' => 'Pages',      'route' => 'admin.pages.index'],
                        ['icon' => 'briefcase','label'=>'Portfolio', 'route' => 'admin.portfolio.index'],
                        ['icon' => 'pen',  'label' => 'Blog',       'route' => 'admin.blog.index'],
                        ['icon' => 'tag',  'label' => 'Services',   'route' => 'admin.services.index'],
                        ['icon' => 'mail', 'label' => 'Inbox',      'route' => 'admin.contacts.index'],
                        ['icon' => 'image','label' => 'Media',      'route' => 'admin.media.index'],
                        ['icon' => 'bar-chart','label'=>'Analytics','route' => 'admin.analytics'],
                        ['icon' => 'users','label' => 'Clients',    'route' => 'admin.clients.index'],
                        ['icon' => 'settings','label'=>'Settings',  'route' => 'admin.settings.index'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors duration-150 {{ request()->routeIs($item['route'].'*') ? 'bg-white/10 text-white font-medium' : 'text-agency-400 hover:text-white hover:bg-white/5' }}">
                        <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            @if($item['icon'] === 'grid')
                                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                            @elseif($item['icon'] === 'file')
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                            @elseif($item['icon'] === 'briefcase')
                                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            @elseif($item['icon'] === 'pen')
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            @elseif($item['icon'] === 'tag')
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>
                            @elseif($item['icon'] === 'mail')
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                            @elseif($item['icon'] === 'image')
                                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
                            @elseif($item['icon'] === 'bar-chart')
                                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                            @elseif($item['icon'] === 'users')
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            @elseif($item['icon'] === 'settings')
                                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                            @endif
                        </svg>
                        <span>{{ $item['label'] }}</span>

                        @if($item['route'] === 'admin.contacts.index')
                            @php $unread = app(\App\Services\Contact\ContactService::class)->getUnreadCount() @endphp
                            @if($unread > 0)
                                <span class="ml-auto text-xs bg-red-500 text-white rounded-full px-1.5 py-0.5 leading-none">{{ $unread }}</span>
                            @endif
                        @endif
                    </a>
                @endforeach
            </nav>

            {{-- User --}}
            <div class="px-3 py-4 border-t border-agency-800">
                <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg">
                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-medium text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-agency-500 truncate">{{ auth()->user()->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-agency-500 hover:text-white transition-colors" title="Logout">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <main class="flex-1 pl-[var(--sidebar-width)] min-h-screen">
            <div class="p-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
