{{-- resources/views/components/sections/blog-card.blade.php --}}
@props(['post'])

<article class="card group reveal">
    {{-- Cover Image --}}
    @if($post->cover_url)
        <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-[16/9] overflow-hidden">
            <img
                data-src="{{ $post->cover_url }}"
                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 9'%3E%3C/svg%3E"
                alt="{{ $post->title }}"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                loading="lazy"
                width="800" height="450"
            >
        </a>
    @endif

    <div class="p-6">
        {{-- Category & Reading time --}}
        <div class="flex items-center justify-between mb-3">
            <div class="flex gap-1.5">
                @foreach($post->categories->take(1) as $cat)
                    <a href="{{ route('blog.category', $cat->slug) }}" class="badge text-xs hover:border-black/30 dark:hover:border-white/30 transition-colors">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-600">{{ $post->reading_time_text }}</span>
        </div>

        {{-- Title --}}
        <h3 class="font-display font-semibold text-base text-black dark:text-white mb-2 leading-snug line-clamp-2 group-hover:underline underline-offset-2 decoration-1">
            <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
        </h3>

        {{-- Excerpt --}}
        @if($post->excerpt)
            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 mb-4">
                {{ $post->excerpt }}
            </p>
        @endif

        {{-- Author & Date --}}
        <div class="flex items-center gap-3 pt-4 border-t border-[var(--color-border)]">
            <div class="w-7 h-7 rounded-full bg-black dark:bg-white flex items-center justify-center flex-shrink-0">
                <span class="text-white dark:text-black text-xs font-bold">
                    {{ substr($post->author?->name ?? 'A', 0, 1) }}
                </span>
            </div>
            <div>
                <div class="text-xs font-medium text-black dark:text-white">{{ $post->author?->name ?? 'Admin' }}</div>
                <div class="text-xs text-gray-400 dark:text-gray-600">
                    {{ $post->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>
</article>
