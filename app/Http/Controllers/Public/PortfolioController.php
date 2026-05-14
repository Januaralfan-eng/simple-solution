<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Portfolio\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;

class PortfolioController extends Controller
{
    public function __construct(
        private readonly PortfolioService $portfolioService,
    ) {}

    public function index(Request $request): View
    {
        $projects   = $this->portfolioService->getPaginated(perPage: 12);
        $categories = $this->portfolioService->getCategories();

        return view('pages.portfolio.index', [
            'seo' => [
                'title'       => 'Portfolio — ' . config('agency.name'),
                'description' => 'Explore our selected projects — websites, apps, and digital experiences.',
                'canonical'   => route('portfolio.index'),
            ],
            'projects'   => $projects,
            'categories' => $categories,
        ]);
    }

    /**
     * HTMX endpoint — returns partial HTML for filtered portfolio grid.
     */
    public function filter(Request $request): View|string
    {
        abort_unless($request->header('HX-Request'), 400);

        $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'page'     => ['nullable', 'integer', 'min:1'],
        ]);

        $projects = $this->portfolioService->getPaginated(
            perPage:  12,
            category: $request->input('category'),
        );

        return view('pages.portfolio._grid', compact('projects'));
    }

    public function show(string $slug): View
    {
        $project = $this->portfolioService->findBySlug($slug);

        abort_if(!$project || !$project->is_published, 404);

        // Track view
        $this->portfolioService->incrementViews($project);

        $related = $this->portfolioService->getRelated($project, limit: 3);

        return view('pages.portfolio.show', [
            'seo' => [
                'title'       => $project->title . ' — Portfolio',
                'description' => $project->excerpt,
                'og_image'    => $project->thumbnail_url,
                'canonical'   => route('portfolio.show', $project->slug),
            ],
            'project' => $project,
            'related' => $related,
        ]);
    }

    /**
     * Toggle like — returns HTMX partial.
     */
    public function like(Request $request, string $slug): Response|string
    {
        abort_unless($request->header('HX-Request'), 400);

        $project = $this->portfolioService->findBySlug($slug);
        abort_if(!$project, 404);

        $likes = $this->portfolioService->toggleLike($project, $request->ip());

        return response()->view('pages.portfolio._like-btn', [
            'project' => $project,
            'liked'   => true,
            'likes'   => $likes,
        ]);
    }
}
