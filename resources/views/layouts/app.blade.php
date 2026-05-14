<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="{{ $darkDefault ?? '' }}"
    x-data
    x-init="$store.theme.init()"
    :class="{ 'dark': $store.theme.dark }"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta --}}
    <title>{{ $seo['title'] ?? config('agency.seo.default_title') }}</title>
    <meta name="description" content="{{ $seo['description'] ?? config('agency.seo.default_description') }}">
    <meta name="robots" content="{{ $seo['robots'] ?? 'index,follow' }}">
    @isset($seo['canonical'])
        <link rel="canonical" href="{{ $seo['canonical'] }}">
    @endisset

    {{-- OpenGraph --}}
    <meta property="og:type"        content="{{ $seo['og_type'] ?? 'website' }}">
    <meta property="og:title"       content="{{ $seo['title'] ?? config('agency.seo.default_title') }}">
    <meta property="og:description" content="{{ $seo['description'] ?? config('agency.seo.default_description') }}">
    <meta property="og:image"       content="{{ $seo['og_image'] ?? asset('images/og-default.jpg') }}">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:site_name"   content="{{ config('agency.name') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $seo['title'] ?? config('agency.seo.default_title') }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? config('agency.seo.default_description') }}">
    <meta name="twitter:image"       content="{{ $seo['og_image'] ?? asset('images/og-default.jpg') }}">

    {{-- Favicons --}}
    <link rel="icon"             href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon"             href="{{ asset('icons/icon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="manifest"         href="{{ asset('site.webmanifest') }}">

    {{-- Preconnect for performance --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://api.fontshare.com">

    {{-- Theme color --}}
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0a0a0a"  media="(prefers-color-scheme: dark)">

    {{-- Schema Markup --}}
    @stack('schema')

    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Page-specific head --}}
    @stack('head')
</head>

<body class="min-h-screen antialiased transition-colors duration-500">

    {{-- Skip Link (Accessibility) --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:rounded-lg focus:bg-black focus:text-white focus:text-sm">
        Skip to main content
    </a>

    {{-- Reading Progress Bar (Blog only) --}}
    @isset($showProgress)
    <div class="fixed top-0 left-0 z-50 h-0.5 bg-black dark:bg-white transition-all duration-100 ease-out"
         id="reading-progress" style="width: 0%"></div>
    @endisset

    {{-- Toast Notifications --}}
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"
         x-data
         x-show="$store.toast.items.length > 0">
        <template x-for="toast in $store.toast.items" :key="toast.id">
            <div class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium shadow-lg animate-fade-up"
                 :class="{
                     'bg-black text-white':          toast.type === 'success',
                     'bg-red-500 text-white':        toast.type === 'error',
                     'bg-amber-400 text-amber-900':  toast.type === 'warning',
                 }">
                <span x-text="toast.message"></span>
                <button @click="$store.toast.remove(toast.id)" class="opacity-70 hover:opacity-100">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    {{-- Navbar --}}
    <x-layout.navbar />

    {{-- Main Content --}}
    <main id="main-content" class="pt-[var(--navbar-height)]">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <x-layout.footer />

    {{-- WhatsApp Floating Button --}}
    @if(config('agency.whatsapp'))
    <a href="https://wa.me/{{ config('agency.whatsapp') }}?text={{ urlencode('Halo, saya ingin bertanya tentang layanan kalian.') }}"
       target="_blank"
       rel="noopener noreferrer"
       class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-110 no-print"
       aria-label="Chat WhatsApp">
        <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
    @endif

    {{-- Page-specific scripts --}}
    @stack('scripts')
</body>
</html>
