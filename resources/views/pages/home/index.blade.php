<x-layouts.app :seo="$seo">

    {{-- HERO SECTION --}}
    <section class="relative min-h-[calc(100vh-var(--navbar-height))] flex items-center justify-center overflow-hidden mesh-gradient">

        {{-- Background Grid --}}
        <div class="absolute inset-0 bg-grid-pattern opacity-40 dark:opacity-20 pointer-events-none"></div>

        {{-- Radial blur blobs --}}
        <div class="absolute top-1/4 -left-20 w-[500px] h-[500px] rounded-full bg-black/3 dark:bg-white/3 blur-[120px] pointer-events-none animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 -right-20 w-[400px] h-[400px] rounded-full bg-black/3 dark:bg-white/3 blur-[100px] pointer-events-none animate-pulse-slow animate-delay-300"></div>

        <div class="container-agency relative z-10 py-24 text-center">

            {{-- Available badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-black/10 dark:border-white/10 text-xs font-medium text-gray-500 dark:text-gray-400 mb-8 animate-fade-up bg-white/60 dark:bg-black/60 backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                Available for new projects
            </div>

            {{-- Main heading --}}
            <h1 class="section-title text-5xl md:text-7xl lg:text-8xl mb-6 animate-fade-up animate-delay-100 text-balance">
                We craft<br>
                <span class="relative inline-block">
                    <span class="relative z-10">digital experiences</span>
                    <span class="absolute -bottom-1 left-0 right-0 h-[3px] bg-black dark:bg-white rounded-full transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></span>
                </span><br>
                that matter.
            </h1>

            {{-- Subtitle --}}
            <p class="section-subtitle mx-auto mb-10 animate-fade-up animate-delay-200">
                Premium creative tech agency. We design and build fast, beautiful,
                SEO-first web applications that convert visitors into clients.
            </p>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 animate-fade-up animate-delay-300">
                <a href="{{ route('portfolio.index') }}" class="btn-primary px-8 py-3.5 text-sm">
                    View Our Work
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M7 17L17 7M17 7H7M17 7v10"/>
                    </svg>
                </a>
                <a href="{{ route('contact') }}" class="btn-secondary px-8 py-3.5 text-sm">
                    Start a Project
                </a>
            </div>

            {{-- Stats --}}
            <div class="flex items-center justify-center gap-8 md:gap-16 mt-20 animate-fade-up animate-delay-400">
                @foreach([
                    ['number' => '50+',  'label' => 'Projects Delivered'],
                    ['number' => '98%',  'label' => 'Client Satisfaction'],
                    ['number' => '4y',   'label' => 'Years of Experience'],
                    ['number' => '24h',  'label' => 'Response Time'],
                ] as $stat)
                <div class="text-center">
                    <div class="font-display font-bold text-2xl md:text-3xl text-black dark:text-white">{{ $stat['number'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>

        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <div class="w-6 h-10 rounded-full border-2 border-black/20 dark:border-white/20 flex items-start justify-center pt-1.5">
                <div class="w-1 h-2.5 rounded-full bg-black/30 dark:bg-white/30 animate-fade-in animate-delay-600"></div>
            </div>
        </div>
    </section>

    {{-- CLIENT LOGO MARQUEE: removed per design decision --}}

    {{-- SERVICES SECTION --}}
    <section id="services" class="py-24 md:py-32">
        <div class="container-agency">
            <div class="max-w-2xl mb-16">
                <div class="section-label mb-4">
                    <span class="w-4 h-px bg-current inline-block"></span>
                    What we do
                </div>
                <h2 class="section-title mb-4 reveal">Services</h2>
                <p class="section-subtitle reveal">
                    End-to-end digital solutions — from strategy and design to development and launch.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($services ?? [] as $i => $service)
                <div class="card p-8 group reveal" style="animation-delay: {{ $i * 80 }}ms">
                    <div class="w-10 h-10 rounded-xl bg-black/5 dark:bg-white/5 flex items-center justify-center mb-6 text-black dark:text-white group-hover:bg-black group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition-all duration-300">
                        {!! $service->icon_svg !!}
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-3 text-black dark:text-white">{{ $service->title }}</h3>
                    <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400 mb-6">{{ $service->excerpt }}</p>
                    <a href="{{ route('services.show', $service->slug) }}"
                       class="inline-flex items-center gap-1.5 text-sm font-medium text-black dark:text-white group-hover:gap-2.5 transition-all duration-200">
                        Learn more
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PORTFOLIO SECTION --}}
    <section id="portfolio" class="py-24 md:py-32 bg-[var(--color-bg-subtle)]">
        <div class="container-agency">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <div class="section-label mb-4">
                        <span class="w-4 h-px bg-current inline-block"></span>
                        Our work
                    </div>
                    <h2 class="section-title reveal">Selected Projects</h2>
                </div>
                <a href="{{ route('portfolio.index') }}" class="btn-secondary self-start md:self-auto">
                    View All
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- Portfolio Grid --}}
            <div id="portfolio-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($featured_projects ?? [] as $project)
                <x-sections.portfolio-card :project="$project" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ABOUT / PROCESS SECTION --}}
    <section id="about" class="py-24 md:py-32">
        <div class="container-agency">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <div class="section-label mb-4">
                        <span class="w-4 h-px bg-current inline-block"></span>
                        How we work
                    </div>
                    <h2 class="section-title mb-6 reveal">Simple process,<br>exceptional results.</h2>
                    <p class="section-subtitle mb-10 reveal">
                        We believe great digital products come from clear thinking and focused execution.
                        Our proven process ensures every project is delivered on time and exceeds expectations.
                    </p>

                    <div class="space-y-6">
                        @foreach([
                            ['step' => '01', 'title' => 'Discovery', 'desc' => 'We dive deep into your goals, audience, and competitive landscape to build a solid foundation.'],
                            ['step' => '02', 'title' => 'Design',    'desc' => 'Wireframes, prototypes, and pixel-perfect UI that reflects your brand and delights users.'],
                            ['step' => '03', 'title' => 'Build',     'desc' => 'Clean, maintainable code with performance and SEO baked in from day one.'],
                            ['step' => '04', 'title' => 'Launch',    'desc' => 'Smooth deployment, thorough testing, and ongoing support to grow your digital presence.'],
                        ] as $i => $step)
                        <div class="flex gap-5 reveal" style="animation-delay: {{ $i * 100 }}ms">
                            <div class="font-mono text-xs font-bold text-gray-300 dark:text-gray-600 pt-1 w-6 flex-shrink-0">{{ $step['step'] }}</div>
                            <div>
                                <h3 class="font-semibold text-base text-black dark:text-white mb-1">{{ $step['title'] }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Visual panel --}}
                <div class="reveal">
                    <div class="relative">
                        <div class="aspect-square rounded-3xl bg-[var(--color-surface)] border border-[var(--color-border)] overflow-hidden">
                            {{-- Decorative abstract art --}}
                            <div class="absolute inset-0 grid grid-cols-3 grid-rows-3 gap-3 p-6 opacity-60">
                                @foreach(range(1,9) as $i)
                                <div class="rounded-xl bg-black/5 dark:bg-white/5 transition-all duration-700" style="animation-delay: {{ $i * 150 }}ms"></div>
                                @endforeach
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="font-display font-bold text-7xl text-black/10 dark:text-white/10">4y</div>
                                    <div class="text-sm text-gray-400">of crafting excellence</div>
                                </div>
                            </div>
                        </div>

                        {{-- Floating badge --}}
                        <div class="absolute -bottom-4 -right-4 glass-card px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                <span class="text-xs font-medium text-black dark:text-white">50+ projects delivered</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- BLOG / INSIGHTS SECTION --}}
    @if(isset($latest_posts) && $latest_posts->count())
    <section id="blog" class="py-24 md:py-32 bg-[var(--color-bg-subtle)]">
        <div class="container-agency">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <div class="section-label mb-4">
                        <span class="w-4 h-px bg-current inline-block"></span>
                        Insights
                    </div>
                    <h2 class="section-title reveal">Latest thinking</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="btn-secondary self-start md:self-auto">
                    Read all articles
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($latest_posts as $post)
                <x-sections.blog-card :post="$post" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- PRICING SECTION --}}
    <section id="pricing" class="py-24 md:py-32">
        <div class="container-agency">
            <div class="text-center max-w-xl mx-auto mb-14">
                <div class="section-label justify-center mb-4">
                    <span class="w-4 h-px bg-current inline-block"></span>
                    Pricing
                    <span class="w-4 h-px bg-current inline-block"></span>
                </div>
                <h2 class="section-title mb-4 reveal">Simple, transparent pricing</h2>
                <p class="section-subtitle mx-auto reveal">No hidden fees. Pick the plan that fits your project.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-5xl mx-auto">
                @foreach($pricing ?? [] as $i => $plan)
                <div class="card p-8 {{ $plan->is_featured ? 'bg-black dark:bg-white ring-1 ring-black dark:ring-white' : '' }} reveal"
                     style="animation-delay: {{ $i * 80 }}ms">
                    <div class="mb-6">
                        <h3 class="font-display font-semibold text-lg mb-1 {{ $plan->is_featured ? 'text-white dark:text-black' : 'text-black dark:text-white' }}">{{ $plan->name }}</h3>
                        <p class="text-sm {{ $plan->is_featured ? 'text-white/60 dark:text-black/60' : 'text-gray-500 dark:text-gray-400' }}">{{ $plan->description }}</p>
                    </div>
                    <div class="mb-8">
                        <span class="font-display font-bold text-4xl {{ $plan->is_featured ? 'text-white dark:text-black' : 'text-black dark:text-white' }}">
                            {{ $plan->price_formatted }}
                        </span>
                        <span class="text-sm {{ $plan->is_featured ? 'text-white/60 dark:text-black/60' : 'text-gray-400' }}"> / project</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        @foreach($plan->features as $feature)
                        <li class="flex items-center gap-2.5 text-sm {{ $plan->is_featured ? 'text-white/80 dark:text-black/80' : 'text-gray-600 dark:text-gray-300' }}">
                            <svg class="w-4 h-4 flex-shrink-0 {{ $plan->is_featured ? 'text-white dark:text-black' : 'text-black dark:text-white' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5"/>
                            </svg>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('contact') }}?plan={{ $plan->slug }}"
                       class="block text-center {{ $plan->is_featured ? 'bg-white dark:bg-black text-black dark:text-white' : 'btn-primary' }} px-6 py-3 rounded-xl text-sm font-medium transition-all duration-200 hover:scale-[1.02]">
                        Get Started
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ SECTION --}}
    <section id="faq" class="py-24 md:py-32 bg-[var(--color-bg-subtle)]">
        <div class="container-agency">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-12">
                    <div class="section-label justify-center mb-4">
                        <span class="w-4 h-px bg-current inline-block"></span>
                        FAQ
                        <span class="w-4 h-px bg-current inline-block"></span>
                    </div>
                    <h2 class="section-title reveal">Common questions</h2>
                </div>

                <div x-data="faq()" class="space-y-2">
                    @foreach($faqs ?? [] as $i => $faq)
                    <div class="card overflow-hidden reveal" style="animation-delay: {{ $i * 60 }}ms">
                        <button
                            @click="toggle({{ $i }})"
                            class="w-full flex items-center justify-between gap-4 p-6 text-left"
                            :aria-expanded="open === {{ $i }}"
                        >
                            <span class="font-medium text-sm text-black dark:text-white">{{ $faq->question }}</span>
                            <svg class="w-4 h-4 flex-shrink-0 text-gray-400 transition-transform duration-200"
                                 :class="{ 'rotate-180': open === {{ $i }} }"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 9l6 6 6-6"/>
                            </svg>
                        </button>
                        <div
                            x-show="open === {{ $i }}"
                            x-collapse
                            class="px-6 pb-6 text-sm text-gray-500 dark:text-gray-400 leading-relaxed"
                        >
                            {{ $faq->answer }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="py-24 md:py-32">
        <div class="container-agency">
            <div class="relative overflow-hidden rounded-3xl bg-black dark:bg-white p-12 md:p-20 text-center">

                {{-- Background grid --}}
                <div class="absolute inset-0 bg-grid-pattern opacity-10 pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="section-label justify-center text-white/40 dark:text-black/40 mb-6">
                        <span class="w-4 h-px bg-current inline-block"></span>
                        Ready to start?
                        <span class="w-4 h-px bg-current inline-block"></span>
                    </div>
                    <h2 class="font-display font-bold text-4xl md:text-6xl text-white dark:text-black mb-6 text-balance">
                        Let's build something<br>extraordinary together.
                    </h2>
                    <p class="text-lg text-white/60 dark:text-black/60 mb-10 max-w-md mx-auto">
                        Tell us about your project and we'll get back to you within 24 hours.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('contact') }}"
                           class="inline-flex items-center gap-2 px-8 py-3.5 bg-white dark:bg-black text-black dark:text-white rounded-xl text-sm font-medium hover:scale-[1.02] transition-transform duration-200">
                            Start a Project
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M7 17L17 7M17 7H7M17 7v10"/>
                            </svg>
                        </a>
                        <a href="https://wa.me/{{ config('agency.whatsapp') }}"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-8 py-3.5 bg-white/10 dark:bg-black/10 text-white dark:text-black rounded-xl text-sm font-medium hover:bg-white/20 dark:hover:bg-black/20 transition-colors duration-200">
                            Chat on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>
