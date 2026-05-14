{{-- resources/views/components/layout/navbar.blade.php --}}
<header
    class="fixed top-0 left-0 right-0 z-40 navbar-glass transition-all duration-300"
    x-data
    x-init="$store.navbar.init()"
    :class="{
        'shadow-sm':             $store.navbar.scrolled,
        '-translate-y-full':     $store.navbar.hidden,
        'translate-y-0':        !$store.navbar.hidden,
    }"
    style="height: var(--navbar-height);"
>
    <div class="container-agency h-full flex items-center justify-between">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 group" aria-label="{{ config('agency.name') }} Home">
            <div class="w-8 h-8 rounded-lg bg-black dark:bg-white flex items-center justify-center transition-transform duration-200 group-hover:scale-95">
                <span class="font-display font-bold text-white dark:text-black text-sm">{{ substr(config('agency.name'), 0, 1) }}</span>
            </div>
            <span class="font-display font-semibold text-base tracking-tight text-black dark:text-white">
                {{ config('agency.name') }}
            </span>
        </a>

        {{-- Desktop Navigation --}}
        <nav class="hidden md:flex items-center gap-1" aria-label="Main navigation">
            @foreach([
                ['label' => 'Services', 'route' => 'services.index'],
                ['label' => 'Portfolio', 'route' => 'portfolio.index'],
                ['label' => 'Blog',      'route' => 'blog.index'],
                ['label' => 'About',     'route' => 'about'],
            ] as $nav)
                <a href="{{ route($nav['route']) }}"
                   class="btn-ghost text-sm {{ request()->routeIs(explode('.', $nav['route'])[0].'*') ? 'text-black dark:text-white font-semibold' : '' }}">
                    {{ $nav['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Right Actions --}}
        <div class="flex items-center gap-2">

            {{-- Dark Mode Toggle --}}
            <button
                @click="$store.theme.toggle()"
                class="w-9 h-9 rounded-lg flex items-center justify-center transition-all duration-200 hover:bg-black/5 dark:hover:bg-white/10"
                :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <svg x-show="!$store.theme.dark" class="w-4 h-4 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
                </svg>
                <svg x-show="$store.theme.dark" class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>

            {{-- CTA Button --}}
            <a href="{{ route('contact') }}" class="hidden md:inline-flex btn-primary text-sm">
                Let's Talk
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M7 17L17 7M17 7H7M17 7v10"/>
                </svg>
            </a>

            {{-- Mobile Menu Toggle --}}
            <button
                @click="$store.navbar.open = !$store.navbar.open"
                class="md:hidden w-9 h-9 rounded-lg flex items-center justify-center hover:bg-black/5 dark:hover:bg-white/10 transition-colors"
                :aria-expanded="$store.navbar.open"
                aria-controls="mobile-menu"
                aria-label="Toggle menu"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path x-show="!$store.navbar.open" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="$store.navbar.open" d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div
        id="mobile-menu"
        x-show="$store.navbar.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="md:hidden absolute top-full left-0 right-0 navbar-glass border-t border-black/5 dark:border-white/5 pb-4"
        @click.outside="$store.navbar.open = false"
    >
        <nav class="container-agency pt-4 flex flex-col gap-1" aria-label="Mobile navigation">
            @foreach([
                ['label' => 'Services', 'route' => 'services.index'],
                ['label' => 'Portfolio', 'route' => 'portfolio.index'],
                ['label' => 'Blog',      'route' => 'blog.index'],
                ['label' => 'About',     'route' => 'about'],
                ['label' => 'Contact',   'route' => 'contact'],
            ] as $nav)
                <a href="{{ route($nav['route']) }}"
                   class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-black/5 dark:hover:bg-white/10 {{ request()->routeIs(explode('.', $nav['route'])[0].'*') ? 'text-black dark:text-white font-semibold' : 'text-gray-600 dark:text-gray-400' }}"
                   @click="$store.navbar.open = false">
                    {{ $nav['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>
