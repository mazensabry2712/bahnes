<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home')));

        // Only include public GET pages that actually exist. Content detail/list
        // URLs will be added when their public routes are implemented.
        foreach ([
            ['name' => 'localized.contact.show', 'parameters' => ['locale' => 'en']],
            ['name' => 'localized.contact.show', 'parameters' => ['locale' => 'ar']],
        ] as $page) {
            if (Route::has($page['name'])) {
                $sitemap->add(Url::create(route($page['name'], $page['parameters'])));
            }
        }

        return response($sitemap->render(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
