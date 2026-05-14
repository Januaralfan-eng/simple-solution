{{-- resources/views/components/layout/footer.blade.php --}}
<footer class="border-t border-black/5 dark:border-white/5 mt-24">
    <div class="container-agency py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

            {{-- Brand --}}
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-black dark:bg-white flex items-center justify-center">
                        <span class="font-display font-bold text-white dark:text-black text-sm">{{ substr(config('agency.name'), 0, 1) }}</span>
                    </div>
                    <span class="font-display font-semibold text-base tracking-tight text-black dark:text-white">{{ config('agency.name') }}</span>
                </a>
                <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400 max-w-xs mb-6">
                    {{ config('agency.tagline') }}. We craft digital products that look great and perform even better.
                </p>
                <div class="flex items-center gap-3">
                    @foreach(config('agency.social', []) as $network => $url)
                        @if($url)
                        <a href="{{ $url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="w-9 h-9 rounded-lg flex items-center justify-center border border-black/10 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white hover:border-black/30 dark:hover:border-white/30 transition-all duration-200"
                           aria-label="{{ ucfirst($network) }}">
                            @if($network === 'github')
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/>
                            </svg>
                            @elseif($network === 'instagram')
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/>
                            </svg>
                            @elseif($network === 'linkedin')
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                            </svg>
                            @endif
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Services Links --}}
            <div>
                <h3 class="font-semibold text-sm mb-4 text-black dark:text-white">Services</h3>
                <ul class="space-y-2.5">
                    @foreach(['Web Development', 'UI/UX Design', 'Brand Identity', 'SEO & Content', 'Digital Strategy'] as $service)
                    <li>
                        <a href="{{ route('services.index') }}"
                           class="text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors duration-150">
                            {{ $service }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Company Links --}}
            <div>
                <h3 class="font-semibold text-sm mb-4 text-black dark:text-white">Company</h3>
                <ul class="space-y-2.5">
                    @foreach([
                        ['label' => 'About',     'route' => 'about'],
                        ['label' => 'Portfolio',  'route' => 'portfolio.index'],
                        ['label' => 'Blog',       'route' => 'blog.index'],
                        ['label' => 'Contact',    'route' => 'contact'],
                        ['label' => 'Privacy',    'route' => 'privacy'],
                    ] as $link)
                    <li>
                        <a href="{{ route($link['route']) }}"
                           class="text-sm text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white transition-colors duration-150">
                            {{ $link['label'] }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

        </div>

        {{-- Bottom bar --}}
        <div class="pt-8 border-t border-black/5 dark:border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-gray-400 dark:text-gray-600">
                &copy; {{ date('Y') }} {{ config('agency.name') }}. All rights reserved.
            </p>
            <div class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-600">
                <span>Built with</span>
                <svg class="w-3 h-3 text-red-400" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
                <span>using Laravel + HTMX</span>
            </div>
        </div>
    </div>
</footer>
