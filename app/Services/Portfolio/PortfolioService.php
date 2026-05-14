<?php

namespace App\Services\Portfolio;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectLike;
use App\Exceptions\PortfolioException;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PortfolioService
{
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get paginated published projects.
     */
    public function getPaginated(int $perPage = 12, ?string $category = null): LengthAwarePaginator
    {
        return Project::query()
            ->published()
            ->with(['categories', 'techStack'])
            ->when($category && $category !== 'all', function ($q) use ($category) {
                $q->whereHas('categories', fn ($c) => $c->where('slug', $category));
            })
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Get featured projects for homepage.
     */
    public function getFeatured(int $limit = 4): Collection
    {
        return Cache::remember("portfolio:featured:{$limit}", self::CACHE_TTL, function () use ($limit) {
            return Project::query()
                ->published()
                ->where('is_featured', true)
                ->with(['categories', 'techStack'])
                ->orderBy('sort_order')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Find single project by slug.
     */
    public function findBySlug(string $slug): ?Project
    {
        return Project::with(['categories', 'techStack', 'gallery'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get related projects (same category, exclude current).
     */
    public function getRelated(Project $project, int $limit = 3): Collection
    {
        $categoryIds = $project->categories->pluck('id');

        return Project::query()
            ->published()
            ->where('id', '!=', $project->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('id', $categoryIds))
            ->limit($limit)
            ->get();
    }

    /**
     * Get all active categories.
     */
    public function getCategories(): Collection
    {
        return Cache::remember('portfolio:categories', self::CACHE_TTL, function () {
            return ProjectCategory::withCount(['projects' => fn ($q) => $q->published()])
                ->having('projects_count', '>', 0)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Increment view counter.
     */
    public function incrementViews(Project $project): void
    {
        try {
            $project->increment('views_count');
        } catch (\Throwable $e) {
            Log::warning('PortfolioService::incrementViews failed', [
                'project_id' => $project->id,
                'message'    => $e->getMessage(),
            ]);
        }
    }

    /**
     * Toggle like from IP — returns new like count.
     */
    public function toggleLike(Project $project, string $ip): int
    {
        try {
            $existing = ProjectLike::where('project_id', $project->id)
                ->where('ip', $ip)
                ->first();

            if ($existing) {
                $existing->delete();
                $project->decrement('likes_count');
            } else {
                ProjectLike::create(['project_id' => $project->id, 'ip' => $ip]);
                $project->increment('likes_count');
            }

            return $project->fresh()->likes_count;

        } catch (\Throwable $e) {
            Log::error('PortfolioService::toggleLike failed', [
                'exception'  => get_class($e),
                'message'    => $e->getMessage(),
                'project_id' => $project->id,
                'ip'         => $ip,
            ]);

            throw PortfolioException::likeFailed($e->getMessage());
        }
    }

    /**
     * Clear portfolio caches (call after CMS update).
     */
    public function clearCache(): void
    {
        Cache::forget('portfolio:categories');
        Cache::forget('portfolio:featured:4');
        Cache::forget('portfolio:featured:6');
    }
}
