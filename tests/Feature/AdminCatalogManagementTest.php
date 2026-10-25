<?php

use App\Models\Product;
use App\Models\User;
use App\Models\WebsiteTemplate;

it('protects product management and validates demo URLs before saving', function () {
    $this->get(route('admin.products.index'))->assertRedirect(route('admin.login'));

    $staff = User::factory()->create(['is_admin' => false, 'role_id' => null]);
    $this->actingAs($staff)->get(route('admin.products.index'))->assertForbidden();

    $admin = User::factory()->create(['is_admin' => true]);

    $payload = [
        'title_en' => 'Internal Business Toolkit',
        'slug_en' => 'internal-business-toolkit',
        'summary_en' => 'A practical toolkit for internal operations.',
        'body_en' => 'Description and included features.',
        'status' => 'draft',
        'sort_order' => 1,
        'demo_url' => 'ftp://example.com/demo',
    ];

    $this->actingAs($admin)->from(route('admin.products.create'))
        ->post(route('admin.products.store'), $payload)
        ->assertRedirect(route('admin.products.create'))
        ->assertSessionHasErrors(['demo_url']);

    expect(Product::query()->count())->toBe(0);

    unset($payload['demo_url']);

    $this->actingAs($admin)->post(route('admin.products.store'), $payload)
        ->assertRedirect();

    expect(Product::query()->where('slug_en', 'internal-business-toolkit')->firstOrFail()->status)->toBe('draft');
});

it('requires complete commercial terms before publishing a product', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->from(route('admin.products.create'))
        ->post(route('admin.products.store'), [
            'title_en' => 'Published Product',
            'slug_en' => 'published-product',
            'summary_en' => 'Product summary.',
            'body_en' => 'Product details.',
            'status' => 'published',
            'sort_order' => 0,
        ])
        ->assertRedirect(route('admin.products.create'))
        ->assertSessionHasErrors(['status']);

    expect(Product::query()->count())->toBe(0);
});

it('protects website template management and rejects non-web demo protocols', function () {
    $this->get(route('admin.templates.index'))->assertRedirect(route('admin.login'));

    $admin = User::factory()->create(['is_admin' => true]);
    $payload = [
        'title_en' => 'Corporate Starter Template',
        'slug_en' => 'corporate-starter-template',
        'summary_en' => 'A starting point for corporate websites.',
        'license_terms_en' => 'Single-business license.',
        'delivery_terms_en' => 'Delivered as a source package.',
        'support_terms_en' => 'Email support for the agreed period.',
        'status' => 'draft',
        'sort_order' => 1,
        'demo_url' => 'ftp://example.com/template',
    ];

    $this->actingAs($admin)->from(route('admin.templates.create'))
        ->post(route('admin.templates.store'), $payload)
        ->assertRedirect(route('admin.templates.create'))
        ->assertSessionHasErrors(['demo_url']);

    expect(WebsiteTemplate::query()->count())->toBe(0);

    unset($payload['demo_url']);

    $this->actingAs($admin)->post(route('admin.templates.store'), $payload)
        ->assertRedirect();

    expect(WebsiteTemplate::query()->where('slug_en', 'corporate-starter-template')->firstOrFail()->status)->toBe('draft');
});

it('requires license delivery and support terms before publishing a website template', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->from(route('admin.templates.create'))
        ->post(route('admin.templates.store'), [
            'title_en' => 'Incomplete Template',
            'slug_en' => 'incomplete-template',
            'summary_en' => 'Template summary.',
            'status' => 'published',
            'sort_order' => 0,
        ])
        ->assertRedirect(route('admin.templates.create'))
        ->assertSessionHasErrors(['status']);

    expect(WebsiteTemplate::query()->count())->toBe(0);
});
