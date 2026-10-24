<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\WebsiteTemplate;
use Illuminate\Http\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $sitemap = Sitemap::create();

        foreach (['en', 'ar'] as $locale) {
            $sitemap->add(Url::create(route('localized.home', ['locale' => $locale])));
            $sitemap->add(Url::create(route('localized.contact.show', ['locale' => $locale])));

            foreach (['services', 'projects', 'products', 'templates', 'insights'] as $section) {
                $sitemap->add(Url::create(route('public.content.index', [
                    'locale' => $locale,
                    'section' => $section,
                ])));

                foreach ($this->publishedRecords($section, $locale) as $record) {
                    $sitemap->add(Url::create(route('public.content.show', [
                        'locale' => $locale,
                        'section' => $section,
                        'slug' => $record->getAttribute("slug_{$locale}"),
                    ])));
                }
            }
        }

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    private function publishedRecords(string $section, string $locale): \Illuminate\Support\Collection
    {
        $query = match ($section) {
            'services' => Service::query()->publishedForLocale($locale),
            'projects' => Project::query()->publishedForLocale($locale),
            'products' => Product::query()->publishedForLocale($locale),
            'templates' => WebsiteTemplate::query()->publishedForLocale($locale),
            'insights' => Insight::query()->publishedForLocale($locale),
            default => throw new \InvalidArgumentException('Unsupported public content section.'),
        };

        return $query->get(['id', "slug_{$locale}"]);
    }
}
