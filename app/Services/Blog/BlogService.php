<?php

namespace App\Services\Blog;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PostTag;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BlogService
{
    private const CACHE_TTL = 1800; // 30 minutes

    /**
     * Get paginated published posts.
     */
    public function getPaginated(
        int     $perPage     = 9,
        ?string $categorySlug = null,
        ?string $tagSlug      = null,
    ): LengthAwarePaginator {
        return Post::query()
            ->published()
            ->with(['author', 'categories', 'tags'])
            ->when($categorySlug, function ($q) use ($categorySlug) {
                $q->whereHas('categories', fn ($c) => $c->where('slug', $categorySlug));
            })
            ->when($tagSlug, function ($q) use ($tagSlug) {
                $q->whereHas('tags', fn ($t) => $t->where('slug', $tagSlug));
            })
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Get featured posts for blog hero.
     */
    public function getFeatured(int $limit = 1): Collection
    {
        return Post::query()
            ->published()
            ->where('is_featured', true)
            ->with(['author', 'categories'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get latest posts for homepage.
     */
    public function getLatest(int $limit = 3): Collection
    {
        return Cache::remember("blog:latest:{$limit}", self::CACHE_TTL, function () use ($limit) {
            return Post::query()
                ->published()
                ->with(['author', 'categories'])
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Search posts by title and content (HTMX).
     */
    public function search(string $query): Collection
    {
        return Post::query()
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('title',   'like', "%{$query}%")
                  ->orWhere('excerpt','like', "%{$query}%")
                  ->orWhere('content','like', "%{$query}%");
            })
            ->with(['categories'])
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();
    }

    /**
     * Find published post by slug.
     */
    public function findPublishedBySlug(string $slug): ?Post
    {
        return Post::query()
            ->published()
            ->with(['author', 'categories', 'tags'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Find category by slug.
     */
    public function findCategory(string $slug): ?PostCategory
    {
        return PostCategory::where('slug', $slug)->first();
    }

    /**
     * Find tag by slug.
     */
    public function findTag(string $slug): ?PostTag
    {
        return PostTag::where('slug', $slug)->first();
    }

    /**
     * Get all post categories.
     */
    public function getCategories(): Collection
    {
        return Cache::remember('blog:categories', self::CACHE_TTL, function () {
            return PostCategory::withCount(['posts' => fn ($q) => $q->published()])
                ->having('posts_count', '>', 0)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get popular tags.
     */
    public function getPopularTags(int $limit = 15): Collection
    {
        return Cache::remember("blog:tags:{$limit}", self::CACHE_TTL, function () use ($limit) {
            return PostTag::withCount(['posts' => fn ($q) => $q->published()])
                ->having('posts_count', '>', 0)
                ->orderByDesc('posts_count')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get related posts by category (exclude current).
     */
    public function getRelated(Post $post, int $limit = 3): Collection
    {
        $categoryIds = $post->categories->pluck('id');

        return Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($categoryIds->isNotEmpty(), function ($q) use ($categoryIds) {
                $q->whereHas('categories', fn ($c) => $c->whereIn('id', $categoryIds));
            })
            ->with(['author', 'categories'])
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Increment view counter.
     */
    public function incrementViews(Post $post): void
    {
        try {
            $post->increment('views_count');
        } catch (\Throwable $e) {
            Log::warning('BlogService::incrementViews failed', [
                'post_id' => $post->id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Calculate reading time in minutes.
     */
    public function readingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int) ceil($wordCount / 200));
    }
}
