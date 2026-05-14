<?php

namespace App\Services\SEO;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class SeoService
{
    /**
     * Generate sitemap data.
     */
    public function getSitemapData(): array
    {
        return Cache::remember('seo:sitemap', 3600, function () {
            $urls = [];

            // Static pages
            foreach (['/', '/about', '/services', '/portfolio', '/blog', '/contact'] as $path) {
                $urls[] = [
                    'loc'        => url($path),
                    'lastmod'    => now()->toAtomString(),
                    'changefreq' => $path === '/' ? 'weekly' : 'monthly',
                    'priority'   => $path === '/' ? '1.0' : '0.8',
                ];
            }

            // Portfolio pages
            Project::published()->select('slug', 'updated_at')->each(function ($p) use (&$urls) {
                $urls[] = [
                    'loc'        => route('portfolio.show', $p->slug),
                    'lastmod'    => $p->updated_at->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority'   => '0.7',
                ];
            });

            // Blog posts
            Post::published()->select('slug', 'updated_at')->each(function ($p) use (&$urls) {
                $urls[] = [
                    'loc'        => route('blog.show', $p->slug),
                    'lastmod'    => $p->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority'   => '0.8',
                ];
            });

            return $urls;
        });
    }

    /**
     * Generate Organization Schema.org JSON-LD.
     */
    public function getOrganizationSchema(): string
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            'name'     => config('agency.name'),
            'url'      => config('app.url'),
            'logo'     => asset('icons/icon.svg'),
            'email'    => config('agency.email'),
            'telephone'=> config('agency.phone'),
            'address'  => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => config('agency.address'),
                'addressCountry'  => 'ID',
            ],
            'sameAs' => array_values(array_filter([
                config('agency.social.github'),
                config('agency.social.instagram'),
                config('agency.social.linkedin'),
            ])),
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }

    /**
     * Generate Article Schema for blog posts.
     */
    public function getArticleSchema(Post $post): string
    {
        $schema = [
            '@context'  => 'https://schema.org',
            '@type'     => 'Article',
            'headline'  => $post->title,
            'description'=> $post->excerpt,
            'image'     => $post->cover_url ?? asset('images/og-default.jpg'),
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified'  => $post->updated_at->toIso8601String(),
            'author'    => [
                '@type' => 'Person',
                'name'  => $post->author?->name ?? config('agency.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name'  => config('agency.name'),
                'logo'  => ['@type' => 'ImageObject', 'url' => asset('icons/icon.svg')],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => route('blog.show', $post->slug),
            ],
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }

    /**
     * Generate BreadcrumbList schema.
     */
    public function getBreadcrumbSchema(array $crumbs): string
    {
        $items = array_map(function ($crumb, $index) {
            return [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $crumb['name'],
                'item'     => $crumb['url'],
            ];
        }, $crumbs, array_keys($crumbs));

        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];

        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
}
