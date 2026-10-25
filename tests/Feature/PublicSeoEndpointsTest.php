<?php

use App\Models\Inquiry;

it('serves a valid sitemap containing only implemented public GET pages', function () {
    $response = $this->get(route('sitemap'));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee(route('localized.home', ['locale' => 'en']), false)
        ->assertSee(route('localized.home', ['locale' => 'ar']), false)
        ->assertSee(route('localized.contact.show', ['locale' => 'en']), false)
        ->assertSee(route('localized.contact.show', ['locale' => 'ar']), false)
        ->assertSee(route('public.content.index', ['locale' => 'en', 'section' => 'services']), false)
        ->assertSee(route('public.content.index', ['locale' => 'ar', 'section' => 'insights']), false)
        ->assertDontSee('/admin-panel', false);

    expect(substr_count($response->getContent(), '<loc>'))->toBe(14);
});

it('serves robots directives with the absolute sitemap URL and admin exclusion', function () {
    $this->get(route('robots'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin-panel/')
        ->assertSee('Sitemap: '.route('sitemap'));
});

it('renders a localized contact form with the package honeypot fields', function () {
    $this->get(route('localized.contact.show', ['locale' => 'ar']))
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertSee('name="my_name_', false)
        ->assertSee('name="valid_from"', false);
});

it('does not store an inquiry when the honeypot is filled', function () {
    config()->set('honeypot.enabled', true);
    config()->set('honeypot.randomize_name_field_name', false);
    config()->set('honeypot.valid_from_timestamp', false);

    $this->post(route('localized.contact.store', ['locale' => 'en']), [
        'name' => 'Spam Bot',
        'email' => 'spam@example.com',
        'type' => 'software',
        'message' => 'This bot should be stopped before it writes to the database.',
        'preferred_locale' => 'en',
        'my_name' => 'automated-spam-value',
    ]);

    expect(Inquiry::query()->count())->toBe(0);
});


it('never exposes private client delivery projects in public pages or the sitemap', function () {
    $client = \\App\\Models\\Client::query()->create([
        'name' => 'Private Customer Name',
        'email' => 'private-customer@example.com',
        'preferred_locale' => 'en',
        'internal_notes' => 'Confidential internal notes',
    ]);
    \\App\\Models\\DeliveryProject::query()->create([
        'client_id' => $client->id,
        'title' => 'Confidential Internal Delivery Project',
        'status' => 'in_progress',
        'progress_percent' => 35,
        'reference' => 'BH-PRJ-PRIVATE',
        'internal_notes' => 'Never public',
    ]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertDontSee('Confidential Internal Delivery Project')
        ->assertDontSee('BH-PRJ-PRIVATE')
        ->assertDontSee('private-customer@example.com');

    $this->get(route('public.content.index', ['locale' => 'en', 'section' => 'projects']))
        ->assertOk()
        ->assertDontSee('Confidential Internal Delivery Project')
        ->assertDontSee('Private Customer Name');
});
