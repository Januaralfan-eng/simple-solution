<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CMS\PageService;
use App\Services\Portfolio\PortfolioService;
use App\Services\Blog\BlogService;
use App\Services\CMS\SettingsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly PageService      $pageService,
        private readonly PortfolioService $portfolioService,
        private readonly BlogService      $blogService,
        private readonly SettingsService  $settingsService,
    ) {}

    public function index(): View
    {
        $settings = $this->settingsService->getPublicSettings();

        return view('pages.home.index', [
            'seo' => [
                'title'       => $settings->seo_title ?? config('agency.seo.default_title'),
                'description' => $settings->seo_description ?? config('agency.seo.default_description'),
                'canonical'   => url('/'),
            ],
            'services'          => $this->pageService->getActiveServices(),
            'featured_projects' => $this->portfolioService->getFeatured(limit: 4),
            'latest_posts'      => $this->blogService->getLatest(limit: 3),
            'clients'           => $this->pageService->getClients(),
            'pricing'           => $this->pageService->getPricingPlans(),
            'faqs'              => $this->pageService->getFaqs(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about.index', [
            'seo' => [
                'title'       => 'About Us — ' . config('agency.name'),
                'description' => 'Learn about our creative tech agency — our story, team, and mission.',
                'canonical'   => route('about'),
            ],
        ]);
    }

    public function privacy(): View
    {
        $page = $this->pageService->findBySlug('privacy-policy');

        return view('pages.privacy.index', [
            'seo'  => [
                'title'  => 'Privacy Policy — ' . config('agency.name'),
                'robots' => 'noindex,follow',
            ],
            'page' => $page,
        ]);
    }
}
