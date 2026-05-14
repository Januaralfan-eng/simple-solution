<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Blog\BlogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogService $blogService,
    ) {}

    public function index(): View
    {
        [$posts, $categories, $tags, $featured] = [
            $this->blogService->getPaginated(perPage: 9),
            $this->blogService->getCategories(),
            $this->blogService->getPopularTags(limit: 15),
            $this->blogService->getFeatured(limit: 1),
        ];

        return view('pages.blog.index', [
            'seo' => [
                'title'       => 'Blog — Insights & Resources | ' . config('agency.name'),
                'description' => 'Articles, case studies, and insights on web development, design, and digital strategy.',
                'canonical'   => route('blog.index'),
            ],
            'posts'      => $posts,
            'categories' => $categories,
            'tags'       => $tags,
            'featured'   => $featured->first(),
        ]);
    }

    /**
     * HTMX search partial.
     */
    public function search(Request $request): View
    {
        abort_unless($request->header('HX-Request'), 400);

        $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
        ]);

        $query  = trim($request->input('q', ''));
        $posts  = $query ? $this->blogService->search($query) : collect();

        return view('pages.blog._search-results', compact('posts', 'query'));
    }

    public function category(string $slug): View
    {
        $category = $this->blogService->findCategory($slug);
        abort_if(!$category, 404);

        $posts = $this->blogService->getPaginated(perPage: 9, categorySlug: $slug);

        return view('pages.blog.index', [
            'seo' => [
                'title'       => $category->name . ' — Blog | ' . config('agency.name'),
                'description' => $category->description ?? "Articles about {$category->name}.",
                'canonical'   => route('blog.category', $slug),
            ],
            'posts'           => $posts,
            'categories'      => $this->blogService->getCategories(),
            'tags'            => $this->blogService->getPopularTags(),
            'active_category' => $category,
        ]);
    }

    public function tag(string $slug): View
    {
        $tag   = $this->blogService->findTag($slug);
        abort_if(!$tag, 404);

        $posts = $this->blogService->getPaginated(perPage: 9, tagSlug: $slug);

        return view('pages.blog.index', [
            'seo' => [
                'title'       => '#' . $tag->name . ' — Blog | ' . config('agency.name'),
                'description' => "Articles tagged with {$tag->name}.",
                'canonical'   => route('blog.tag', $slug),
            ],
            'posts'      => $posts,
            'categories' => $this->blogService->getCategories(),
            'tags'       => $this->blogService->getPopularTags(),
            'active_tag' => $tag,
        ]);
    }

    public function show(string $slug): View
    {
        $post = $this->blogService->findPublishedBySlug($slug);
        abort_if(!$post, 404);

        $this->blogService->incrementViews($post);

        $related = $this->blogService->getRelated($post, limit: 3);

        return view('pages.blog.show', [
            'seo'         => [
                'title'       => $post->seo_title ?? $post->title . ' | ' . config('agency.name'),
                'description' => $post->seo_description ?? $post->excerpt,
                'og_image'    => $post->cover_url,
                'og_type'     => 'article',
                'canonical'   => route('blog.show', $post->slug),
            ],
            'showProgress' => true,
            'post'         => $post,
            'related'      => $related,
        ]);
    }
}
