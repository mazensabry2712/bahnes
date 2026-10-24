<?php

use App\Models\Service;
use App\Models\ServiceLine;
use App\Models\User;

function makeServiceAdmin(): User
{
    return User::factory()->create(['is_admin' => true]);
}

function makeServiceLine(): ServiceLine
{
    return ServiceLine::query()->create([
        'key' => 'software-digital-solutions',
        'name_en' => 'Software & Digital Solutions',
        'name_ar' => 'الحلول البرمجية والرقمية',
    ]);
}

it('requires admin access to view and create services', function () {
    $this->get(route('admin.services.index'))
        ->assertRedirect(route('admin.login'));

    $this->get(route('admin.services.create'))
        ->assertRedirect(route('admin.login'));

    $this->actingAs(User::factory()->create(['is_admin' => false]))
        ->get(route('admin.services.index'))
        ->assertForbidden();
});

it('lets an administrator create a draft service in English and Arabic', function () {
    $line = makeServiceLine();

    $this->actingAs(makeServiceAdmin())
        ->post(route('admin.services.store'), [
            'service_line_id' => $line->id,
            'title_en' => 'Managed IT Support',
            'slug_en' => 'managed-it-support',
            'summary_en' => 'Practical technology support for businesses.',
            'body_en' => 'Support scope and delivery details.',
            'title_ar' => 'الدعم الفني للشركات',
            'slug_ar' => 'al-dam-al-fanni',
            'summary_ar' => 'دعم تقني عملي للشركات.',
            'body_ar' => 'تفاصيل نطاق الدعم والخدمة.',
            'status' => 'draft',
            'sort_order' => 10,
        ])
        ->assertRedirect();

    $service = Service::query()->firstOrFail();

    expect($service->status)->toBe('draft')
        ->and($service->titleFor('en'))->toBe('Managed IT Support')
        ->and($service->titleFor('ar'))->toBe('الدعم الفني للشركات')
        ->and(Service::query()->publishedForLocale('en')->whereKey($service->id)->exists())->toBeFalse()
        ->and(Service::query()->publishedForLocale('ar')->whereKey($service->id)->exists())->toBeFalse();
});

it('requires a complete locale before publishing a service', function () {
    $line = makeServiceLine();

    $this->actingAs(makeServiceAdmin())
        ->from(route('admin.services.create'))
        ->post(route('admin.services.store'), [
            'service_line_id' => $line->id,
            'title_en' => 'Security Systems',
            'slug_en' => '',
            'title_ar' => '',
            'slug_ar' => '',
            'status' => 'published',
            'sort_order' => 0,
        ])
        ->assertRedirect(route('admin.services.create'))
        ->assertSessionHasErrors(['slug_en', 'status']);

    expect(Service::query()->count())->toBe(0);
});

it('publishes only in locales with a complete title and slug', function () {
    $line = makeServiceLine();

    $this->actingAs(makeServiceAdmin())
        ->post(route('admin.services.store'), [
            'service_line_id' => $line->id,
            'title_en' => 'Security Systems',
            'slug_en' => 'security-systems',
            'title_ar' => '',
            'slug_ar' => '',
            'status' => 'published',
            'sort_order' => 0,
        ])
        ->assertRedirect();

    $service = Service::query()->firstOrFail();

    expect(Service::query()->publishedForLocale('en')->whereKey($service->id)->exists())->toBeTrue()
        ->and(Service::query()->publishedForLocale('ar')->whereKey($service->id)->exists())->toBeFalse();
});

it('updates a service and supports archiving it', function () {
    $line = makeServiceLine();
    $service = Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Network Installation',
        'slug_en' => 'network-installation',
        'status' => 'draft',
        'sort_order' => 0,
    ]);

    $this->actingAs(makeServiceAdmin())
        ->put(route('admin.services.update', $service), [
            'service_line_id' => $line->id,
            'title_en' => 'Business Network Installation',
            'slug_en' => 'business-network-installation',
            'status' => 'archived',
            'sort_order' => 5,
        ])
        ->assertRedirect(route('admin.services.edit', $service));

    expect($service->fresh()->title_en)->toBe('Business Network Installation')
        ->and($service->fresh()->status)->toBe('archived');
});

it('rejects duplicate slugs and unsupported publication statuses', function () {
    $line = makeServiceLine();
    Service::query()->create([
        'service_line_id' => $line->id,
        'title_en' => 'Existing service',
        'slug_en' => 'existing-service',
        'status' => 'draft',
        'sort_order' => 0,
    ]);

    $this->actingAs(makeServiceAdmin())
        ->from(route('admin.services.create'))
        ->post(route('admin.services.store'), [
            'service_line_id' => $line->id,
            'title_en' => 'Second service',
            'slug_en' => 'existing-service',
            'status' => 'not-a-status',
            'sort_order' => 0,
        ])
        ->assertRedirect(route('admin.services.create'))
        ->assertSessionHasErrors(['slug_en', 'status']);

    expect(Service::query()->count())->toBe(1);
});
