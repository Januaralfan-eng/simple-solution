{{-- resources/views/components/sections/portfolio-card.blade.php --}}
@props(['project'])

<article class="card group cursor-pointer reveal"
         onclick="window.location='{{ route('portfolio.show', $project->slug) }}'">

    {{-- Thumbnail --}}
    <div class="aspect-[16/10] overflow-hidden bg-[var(--color-surface)] relative">
        @if($project->thumbnail_url)
            <img
                data-src="{{ $project->thumbnail_url }}"
                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 10'%3E%3C/svg%3E"
                alt="{{ $project->title }}"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                loading="lazy"
                width="800"
                height="500"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <span class="font-display font-bold text-4xl text-black/10 dark:text-white/10">
                    {{ substr($project->title, 0, 1) }}
                </span>
            </div>
        @endif

        {{-- Overlay on hover --}}
        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-2 group-hover:translate-y-0">
                <span class="glass px-4 py-2 rounded-full text-xs font-medium text-white">View Project</span>
            </div>
        </div>

        {{-- Featured badge --}}
        @if($project->is_featured)
            <div class="absolute top-3 left-3">
                <span class="badge bg-black/80 text-white border-transparent text-xs">Featured</span>
            </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="p-6">
        {{-- Categories --}}
        @if($project->categories->count())
            <div class="flex gap-1.5 mb-3">
                @foreach($project->categories->take(2) as $cat)
                    <span class="badge text-xs">{{ $cat->name }}</span>
                @endforeach
            </div>
        @endif

        <h3 class="font-display font-semibold text-lg text-black dark:text-white mb-2 group-hover:underline underline-offset-2 decoration-1">
            {{ $project->title }}
        </h3>

        @if($project->excerpt)
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-4 line-clamp-2">
                {{ $project->excerpt }}
            </p>
        @endif

        {{-- Tech stack --}}
        @if($project->tech_stack)
            <div class="flex flex-wrap gap-1.5 mb-4">
                @foreach(array_slice($project->tech_stack, 0, 4) as $tech)
                    <span class="tech-badge">{{ $tech }}</span>
                @endforeach
                @if(count($project->tech_stack) > 4)
                    <span class="tech-badge">+{{ count($project->tech_stack) - 4 }}</span>
                @endif
            </div>
        @endif

        {{-- Footer --}}
        <div class="flex items-center justify-between pt-4 border-t border-[var(--color-border)]">
            <div class="flex items-center gap-4 text-xs text-gray-400 dark:text-gray-600">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    {{ number_format($project->views_count) }}
                </span>

                {{-- Like button (HTMX) --}}
                <span
                    hx-post="{{ route('portfolio.like', $project->slug) }}"
                    hx-swap="outerHTML"
                    hx-target="this"
                    class="flex items-center gap-1 cursor-pointer hover:text-black dark:hover:text-white transition-colors"
                    onclick="event.stopPropagation()"
                >
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                    {{ number_format($project->likes_count) }}
                </span>
            </div>

            <div class="flex items-center gap-2">
                @if($project->github_url)
                    <a href="{{ $project->github_url }}"
                       target="_blank" rel="noopener noreferrer"
                       onclick="event.stopPropagation()"
                       class="btn-ghost p-1.5 rounded-lg"
                       aria-label="View source on GitHub">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/>
                        </svg>
                    </a>
                @endif
                @if($project->project_url)
                    <a href="{{ $project->project_url }}"
                       target="_blank" rel="noopener noreferrer"
                       onclick="event.stopPropagation()"
                       class="btn-ghost p-1.5 rounded-lg"
                       aria-label="View live demo">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</article>
