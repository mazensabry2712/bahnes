<?php

use App\Models\Product;
use App\Models\User;
use App\Models\WebsiteTemplate;

it('requires admin access to the product and template catalogs', function () {
    $this->get(route('admin.products.index'))->assertRedirect(route('admin.login'));
    $this->get(route('admin.templates.index'))->assertRedirect(route('admin.login'));
});

it('stores a product draft without making it public', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.products.store'), [
        'title_en' => 'Inventory Control',
        'slug_en' => 'inventory-control',
        'summary_en' => 'A business inventory product.',
        'body_en' => 'Core inventory workflows.',
        'status' => 'draft',
        'sort_order' => 0,
    ])->assertRedirect();

    $product = Product::query()->firstOrFail();

    expect($product->status)->toBe('draft')
        ->and(Product::query()->publishedForLocale('en')->whereKey($product->id)->exists())->toBeFalse();
});

it('requires license, delivery and support terms before publishing a product', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->from(route('admin.products.create'))->post(route('admin.products.store'), [
        'title_en' => 'Inventory Control',
        'slug_en' => 'inventory-control',
        'summary_en' => 'A business inventory product.',
        'status' => 'published',
        'sort_order' => 0,
    ])->assertRedirect(route('admin.products.create'))->assertSessionHasErrors(['status']);

    expect(Product::query()->count())->toBe(0);
});

it('publishes a product only for a locale with complete commercial terms', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.products.store'), [
        'title_en' => 'Inventory Control',
        'slug_en' => 'inventory-control',
        'summary_en' => 'A business inventory product.',
        'license_terms_en' => 'Single business license.',
        'delivery_terms_en' => 'Delivered after a discovery call.',
        'support_terms_en' => 'Email support during the agreed support period.',
        'status' => 'published',
        'sort_order' => 0,
    ])->assertRedirect();

    $product = Product::query()->firstOrFail();

    expect(Product::query()->publishedForLocale('en')->whereKey($product->id)->exists())->toBeTrue()
        ->and(Product::query()->publishedForLocale('ar')->whereKey($product->id)->exists())->toBeFalse();
});

it('keeps product slugs unique and supports archiving', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $product = Product::query()->create([
        'title_en' => 'First product',
        'slug_en' => 'first-product',
        'status' => 'draft',
    ]);

    $this->actingAs($admin)->put(route('admin.products.update', $product), [
        'title_en' => 'First product archived',
        'slug_en' => 'first-product',
        'status' => 'archived',
        'sort_order' => 0,
    ])->assertRedirect(route('admin.products.edit', $product));

    expect($product->fresh()->status)->toBe('archived');
});

it('stores website templates separately from software products', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.templates.store'), [
        'title_en' => 'Corporate Website Kit',
        'slug_en' => 'corporate-website-kit',
        'summary_en' => 'A reusable corporate website template.',
        'included_pages_en' => 'Home, About, Services, Contact.',
        'license_terms_en' => 'One website license.',
        'delivery_terms_en' => 'Files delivered after payment terms are agreed.',
        'support_terms_en' => 'Customization and support are quoted separately.',
        'status' => 'published',
        'sort_order' => 0,
    ])->assertRedirect();

    expect(WebsiteTemplate::query()->count())->toBe(1)
        ->and(Product::query()->count())->toBe(0)
        ->and(WebsiteTemplate::query()->publishedForLocale('en')->exists())->toBeTrue();
});

it('does not publish a template without required commercial terms', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->from(route('admin.templates.create'))->post(route('admin.templates.store'), [
        'title_en' => 'Minimal Website Kit',
        'slug_en' => 'minimal-website-kit',
        'summary_en' => 'A reusable template.',
        'status' => 'published',
        'sort_order' => 0,
    ])->assertRedirect(route('admin.templates.create'))->assertSessionHasErrors(['status']);

    expect(WebsiteTemplate::query()->count())->toBe(0);
});
